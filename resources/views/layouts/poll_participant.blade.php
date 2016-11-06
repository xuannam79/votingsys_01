<div class="form-group">
    {{ Form::label(trans('polls.label_for.invite'), trans('polls.label.invite')) }}
    <br>
    {{
        Form::text('member', null, [
            'id' => 'member',
            'class' => 'form-control',
            'placeholder' => trans('polls.placeholder.email_participant'),
            'data-role' => 'tagsinput',
            'onkeyup' => 'validateParticipant()',
        ])
    }}
    <div class="error_participant"></div>
</div>

