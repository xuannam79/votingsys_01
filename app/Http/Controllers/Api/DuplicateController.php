<?php
namespace App\Http\Controllers\Api;

use App\Models\Poll;
use App\RepositoriesApi\Contracts\PollRepositoryInterface;
use Illuminate\Http\Request;

class DuplicateController extends ApiController
{
    private $pollRepository;

    public function __construct(PollRepositoryInterface $pollRepository)
    {
        $this->pollRepository = $pollRepository;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     * @return \Illuminate\Http\Response
     * optionOldImage is array url of image, count element more one
     */
    public function store(Request $request)
    {
        $input = $request->only(
            'title', 'location', 'description', 'name', 'email', 'chatwork_id', 'type', 'closingTime',
            'optionText', 'optionImage', 'oldImage', 'optionOldImage',
            'setting', 'value', 'setting_child',
            'member'
        );
        $input['page'] = 'duplicate';
        $data = $this->pollRepository->store($input);

        if (!$data) {
            return $this->falseJson(API_RESPONSE_CODE_UNPROCESSABLE, trans('polls.message.duplicate_poll_error'));
        }

        return $this->trueJson($data);
    }
}
