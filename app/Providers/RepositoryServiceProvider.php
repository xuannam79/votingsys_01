<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\Poll\PollRepository;
use App\Repositories\Poll\PollRepositoryInterface;
use App\Repositories\Vote\VoteRepository;
use App\Repositories\Vote\VoteRepositoryInterface;
use App\Repositories\Comment\CommentRepository;
use App\Repositories\Comment\CommentRepositoryInterface;
use App\Repositories\Activity\ActivityRepository;
use App\Repositories\Activity\ActivityRepositoryInterface;
use App\Repositories\Link\LinkRepository;
use App\Repositories\Link\LinkRepositoryInterface;
use App\Repositories\Participant\ParticipantRepository;
use App\Repositories\Participant\ParticipantRepositoryInterface;
use App\Repositories\ParticipantVote\ParticipantVoteRepository;
use App\Repositories\ParticipantVote\ParticipantVoteRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        App::bind(UserRepositoryInterface::class, UserRepository::class);
        App::bind(PollRepositoryInterface::class, PollRepository::class);
        App::bind(VoteRepositoryInterface::class, VoteRepository::class);
        App::bind(CommentRepositoryInterface::class, CommentRepository::class);
        App::bind(ActivityRepositoryInterface::class, ActivityRepository::class);
        App::bind(LinkRepositoryInterface::class, LinkRepository::class);
        App::bind(ParticipantRepositoryInterface::class, ParticipantRepository::class);
        App::bind(ParticipantVoteRepositoryInterface::class, ParticipantVoteRepository::class);
    }
}
