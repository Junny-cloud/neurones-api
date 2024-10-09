<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index(Request $request)
    {

        $query = Post::where('user_id', Auth::id());

        if ($request->has('q')) {
            $q = $request->input('q');
            $query->where(function ($queryBuilder) use ($q) {
                $queryBuilder->where('title', 'like', "%$q%")
                             ->orWhere('content', 'like', "%$q%")
                             ->orWhere('slug', 'like', "%$q%");
            });
        }


        $sortBy = $request->input('sortBy', 'created_at');
        $order = $request->input('order', 'desc');

        if (!in_array($sortBy, ['title', 'content', 'created_at', 'last_update'])) {
            return response()->json(['error' => 'Invalid sortBy field'], 400);
        }

        if (!in_array($order, ['asc', 'desc'])) {
            return response()->json(['error' => 'Invalid order parameter'], 400);
        }

        $query->orderBy($sortBy, $order);

        if ($request->has('skip')) {
            $query->skip($request->input('skip'));
        }

        $limit = $request->input('limit', 10);
        $posts = $query->paginate($limit);

        return response()->json($posts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'image_file' => 'nullable|image',
        ]);

        $path = null;
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('images', 'public');
        }

        $post = Post::create([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'image_path' => $path,
            'user_id' => Auth::id(),
        ]);

        return response()->json($post, 201);
    }

    public function showById($id)
    {
        $post = Post::where('user_id', Auth::id())->findOrFail($id);

        return response()->json($post);
    }

    public function showBySlug($slug)
    {
        $post = Post::where('user_id', Auth::id())->where('slug', $slug)->firstOrFail();

        return response()->json($post);
    }

    public function update(Request $request, $id)
    {
        $post = Post::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'image_file' => 'nullable|image',
        ]);

        if ($request->hasFile('image_file')) {
            Storage::delete($post->image_path);
            $post->image_path = $request->file('image_file')->store('images', 'public');
        }

        $post->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
        ]);

        return response()->json($post);
    }

    public function destroy($id)
    {
        $post = Post::where('user_id', Auth::id())->findOrFail($id);
        Storage::delete($post->image_path);
        $post->delete();

        return response()->json(['message' => 'Post deleted']);
    }
}
