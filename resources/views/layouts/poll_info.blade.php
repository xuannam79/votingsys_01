@if (isset($page) && ($page == "edit" || $page == "manager"))
    {{
       Form::open([
           'route' => ['user-poll.update', $poll->id],
           'method' => 'PUT',
           'id' => 'form_update_poll_info',
           'role' => 'form',
           'onsubmit' => 'return updatePollInfo()',
       ])
    }}
@endif
<div class="row">
<!-- NAME -->
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 col-xs-info">
        <div class="form-group">
            <div class="input-group required">
                <span class="input-group-addon">
                    <i class="fa fa-user" aria-hidden="true"></i>
                </span>
                {{
                    Form::text('name', (isset($poll) && $poll && $poll->user_id)
                                        ? $poll->user->name
                                        : ((isset($poll) && $poll->name)
                                            ? $poll->name
                                            : (auth()->user() ? auth()->user()->name : null)), [
                        'class' => 'form-control',
                        'id' => 'name',
                        'placeholder' => trans('polls.placeholder.full_name'),
                        'readonly' => (auth()->user() && auth()->user()->name) ? true : null,
                    ])
                }}
            </div>
        </div>
    </div>
<!-- EMAIL -->
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 col-xs-info">
        <div class="form-group">
            <div class="input-group required">
                <span class="input-group-addon">
                    <i class="fa fa-envelope" aria-hidden="true"></i>
                </span>
                {{
                    Form::text('email', (isset($poll) && $poll->user_id) ? $poll->user->email : ((isset($poll) && $poll->email) ? $poll->email : (auth()->user() ? auth()->user()->email : null)), [
                        'class' => 'form-control',
                        'id' => 'email',
                        'placeholder' => trans('polls.placeholder.email'),
                        'readonly' => (auth()->user() && auth()->user()->email) ? true : null,
                    ])
                }}
            </div>
            <div class="form-group">
                <div class="error_email"></div>
            </div>
        </div>
    </div>
</div>
<div class="row">
<!-- TITLE -->
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 col-xs-info">
        <div class="form-group">
            <div class="input-group required">
                <span class="input-group-addon">
                    <b>T</b>
                </span>
                {{
                    Form::text('title', (isset($poll) && $poll) ? $poll->title : null, [
                        'class' => 'form-control',
                        'id' => 'title',
                        'placeholder' => trans('polls.placeholder.title'),
                    ])
                }}
            </div>
        </div>
    </div>
<!-- TYPE -->
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 col-xs-info">
        <div class="form-group">
            {{ Form::select('type', $data['viewData']['types'],
                (isset($poll) && $poll) ? ($poll->multiple == trans('polls.label.multiple_choice') ? config('settings.type_poll.multiple_choice') : config('settings.type_poll.single_choice')): null, ['class' => 'form-control']) }}
        </div>
    </div>
</div>

<!-- DESCRIPTION -->
<div class="form-group">
    {{
        Form::textarea('description', (isset($poll) && $poll) ? $poll->description : null, [
            'class' => 'form-control',
            'id' => 'description',
            'placeholder' => trans('polls.placeholder.description'),
            'rows' => 2,
        ])
    }}
</div>
<div class="row">
<!-- TIME CLOSE -->
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 col-xs-info">
        <div class="form-group">
            <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-clock-o" aria-hidden="true"></i>
            </span>
                {{
                    Form::text('closingTime', (isset($poll) && $poll) ? $poll->date_close : null, [
                        'class' => 'form-control',
                        'id' => 'time_close_poll',
                        'placeholder' => trans('polls.placeholder.time_close')
                    ])
                }}
            </div>
        </div>
    </div>
<!-- LOCATION -->
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 col-xs-info">
        <div class="input-group">
            <span class="input-group-addon">
                <i class="fa fa-map-marker" aria-hidden="true"></i>
            </span>
            {{
                Form::text('location', (isset($poll) && $poll) ? $poll->location : null, [
                    'class' => 'form-control',
                    'id' => 'location',
                    'placeholder' => trans('polls.placeholder.location'),
                    'onfocus' => 'getCurrentLocation()',
                ])
            }}
        </div>
        <div id="map"></div>
    </div>
</div>
@if (isset($page) && $page == "edit")
    <input type="submit" class="btn btn-success btn-edit-info btn-xs" name="btn_edit" value="{{ trans('polls.button.save_info') }}">
    <a href="{{ $poll->getAdminLink() }}" class="btn btn-success btn-back-edit btn-xs">{{ trans('polls.button.edit_back') }}</a>
    {{ Form::close() }}
@endif
@if (isset($page) && $page == "manager")
    <input type="submit" class="btn btn-success btn-edit-info" name="btn_edit" value="{{ trans('polls.button.save_info') }}">
    {{ Form::close() }}
@endif
