<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Repositories\Comment\CommentRepositoryInterface;
use App\Repositories\Poll\PollRepositoryInterface;
use App\Repositories\Activity\ActivityRepositoryInterface;

class CommentController extends Controller
{
    protected $commentRepository;
    protected $pollRepository;
    protected $activityRepository;

    public function __construct(
        CommentRepositoryInterface $commentRepository,
        PollRepositoryInterface $pollRepository,
        ActivityRepositoryInterface $activityRepository
    ) {
        $this->commentRepository = $commentRepository;
        $this->pollRepository = $pollRepository;
        $this->activityRepository = $activityRepository;
    }

    public function store(Request $request)
    {
        if ($request->ajax()) {
            $inputs = $request->only('poll_id', 'content', 'name');
            $poll = $this->pollRepository->find($inputs['poll_id']);

            if (auth()->check()) {
                $inputs['user_id'] = auth()->user()->id;
            }

            $comment = $this->commentRepository->create($inputs);
            $activity = [
                'poll_id' => $inputs['poll_id'],
                'type' => config('settings.activity.added_a_comment'),
            ];
            $imageComment = asset(config('settings.image_default_path'));

            if (isset($inputs['user_id'])) {
                $activity['user_id'] = $inputs['user_id'];
                $activity['name'] = $inputs['name'];

                if (auth()->user()->name == $inputs['name']) {
                    $imageComment = auth()->user()->getAvatarPath();
                }
            }

            $this->activityRepository->create($activity);
            $html = view('user.poll.comment_layouts', [
                'commentId' => $comment->id,
                'content' => $inputs['content'],
                'name' => $inputs['name'],
                'poll' => $poll,
                'createdAt' => $comment->created_at->diffForHumans(),
                'imageComment' => $imageComment,
            ])->render();
            $result = [
                'success' => true,
                'html' => $html,
            ];

            return response()->json($result);
        }

        return response()->json(['success' => false]);
    }

    public function destroy($id, Request $request)
    {
        if ($request->ajax()) {
            $inputs = $request->only('comment_id', 'poll_id');

            if (auth()->check()) {
                $inputs['user_id'] = auth()->user()->id;
            }

            $this->commentRepository->delete($inputs['comment_id']);
            $activity = [
                'poll_id' => $inputs['poll_id'],
                'type' => config('settings.activity.delete_comment'),
            ];

            if (isset($inputs['user_id'])) {
                $activity['user_id'] = $inputs['user_id'];
            }

            $this->activityRepository->create($activity);

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }
}
