<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Services\ConversationService;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function __construct(
        protected ConversationService $conversationService
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();

        $conversations = Conversation::whereHas('participants', fn ($q) => $q->where('user_id', $user->id))
            ->with(['participants', 'latestMessage.sender'])
            ->withCount(['messages as unread_count' => function ($q) use ($user) {
                $q->where('sender_id', '!=', $user->id)->whereNull('read_at');
            }])
            ->orderByDesc(
                Message::select('created_at')
                    ->whereColumn('conversation_id', 'conversations.id')
                    ->latest()
                    ->limit(1)
            )
            ->paginate(15);

        $users = User::where('id', '!=', $user->id)->orderBy('name')->get();

        return view('conversations.index', compact('conversations', 'users'));
    }

    public function create(Request $request)
    {
        $users = User::where('id', '!=', $request->user()->id)
            ->orderBy('name')
            ->get();

        return view('conversations.create', compact('users'));
    }

    public function show(Conversation $conversation, Request $request)
    {
        $this->authorize('view', $conversation);

        $conversation->load('participants');

        if ($request->ajax() || $request->wantsJson() || $request->has('after')) {
            $query = Message::where('conversation_id', $conversation->id)
                ->with('sender')
                ->orderBy('created_at')
                ->orderBy('id');

            if ($request->filled('after')) {
                $query->where('id', '>', (int) $request->input('after'));
            }

            return response()->json(['messages' => $query->get()]);
        }

        $this->conversationService->markRead($conversation, $request->user());

        $messages = Message::where('conversation_id', $conversation->id)
            ->with('sender')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        return view('conversations.show', compact('conversation', 'messages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'participant_id' => ['nullable', 'exists:users,id', 'different:' . $request->user()->id],
            'user_id' => ['nullable', 'exists:users,id', 'different:' . $request->user()->id],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:5000'],
        ]);

        $participantId = $validated['participant_id'] ?? $validated['user_id'] ?? null;

        if (!$participantId) {
            return back()->withErrors(['participant_id' => 'Please select who you want to message.'])->withInput();
        }

        $other = User::findOrFail($participantId);

        $conversation = $this->conversationService->findOrCreateDirect(
            $request->user(),
            $other,
            $validated['subject'] ?? null
        );

        return redirect()
            ->route('conversations.show', $conversation)
            ->with('success', 'Chat opened. Type your message below and press Enter to send.');
    }

    public function sendMessage(Request $request, Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $message = $this->conversationService->sendMessage(
            $conversation,
            $request->user(),
            $validated['body']
        );

        event(new MessageSent($message->load('sender')));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => [
                    'id' => $message->id,
                    'body' => $message->body,
                    'sender_id' => $message->sender_id,
                    'created_at' => $message->created_at->toIso8601String(),
                    'sender' => [
                        'id' => $message->sender->id,
                        'name' => $message->sender->name,
                    ],
                ],
            ]);
        }

        return redirect()
            ->route('conversations.show', $conversation)
            ->with('success', 'Message sent.');
    }

    public function markRead(Request $request, Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $this->conversationService->markRead($conversation, $request->user());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return back();
    }
}
