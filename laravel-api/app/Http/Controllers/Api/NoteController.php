<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Models\Note;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    /**
     * List all notes belonging to the authenticated user.
     * Supports optional search/filter by title and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $notes = Note::forUser($request->user()->id)
            ->searchByTitle($request->query('search'))
            ->latest()
            ->paginate($request->query('per_page', 15));

        return response()->json($notes);
    }

    /**
     * Create a new note for the authenticated user.
     */
    public function store(StoreNoteRequest $request): JsonResponse
    {
        $note = Note::create([
            'user_id' => $request->user()->id,
            'title'   => $request->validated('title'),
            'content' => $request->validated('content'),
        ]);

        return response()->json([
            'message' => 'Note created successfully.',
            'note'    => $note,
        ], 201);
    }

    /**
     * View a single note (must belong to the authenticated user).
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $note = Note::forUser($request->user()->id)->find($id);

        if (! $note) {
            return response()->json([
                'message' => 'Note not found.',
            ], 404);
        }

        return response()->json([
            'note' => $note,
        ]);
    }

    /**
     * Update a note (must belong to the authenticated user).
     */
    public function update(UpdateNoteRequest $request, int $id): JsonResponse
    {
        $note = Note::forUser($request->user()->id)->find($id);

        if (! $note) {
            return response()->json([
                'message' => 'Note not found.',
            ], 404);
        }

        $note->update($request->only(['title', 'content']));

        return response()->json([
            'message' => 'Note updated successfully.',
            'note'    => $note->fresh(),
        ]);
    }

    /**
     * Delete a note (must belong to the authenticated user).
     * Uses soft delete — the note is trashed, not permanently removed.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $note = Note::forUser($request->user()->id)->find($id);

        if (! $note) {
            return response()->json([
                'message' => 'Note not found.',
            ], 404);
        }

        $note->delete(); // Soft delete

        return response()->json([
            'message' => 'Note deleted successfully.',
        ]);
    }
}
