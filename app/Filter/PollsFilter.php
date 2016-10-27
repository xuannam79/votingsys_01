<?php

namespace App\Filter;

use App\QueryFilter;

class PollsFilter extends QueryFilter
{
    public function input()
    {
        return parent::filters();
    }

    public function name($input = '')
    {
        $this->builder->join('users', 'users.id', '=', 'polls.user_id');

        if (! $input) {
            return $this;
        }

        return $this->builder->where('users.name', 'LIKE', '%' . $input . '%');
    }

    public function email($input = '')
    {
        if (! $input) {
            return $this;
        }

        return $this->builder->where('users.email', 'LIKE', '%' . $input . '%');
    }

    public function title($input = '')
    {
        if (! $input) {
            return $this;
        }

        return $this->builder->where('polls.title', 'LIKE', '%' . $input . '%');
    }

    public function type($input = '')
    {
        if ($input == config('settings.type.single_choice') ||
            $input == config('settings.type.multiple_choice')) {
            return $this->builder->where('polls.multiple', $input);
        }

        return $this;
    }

    public function status($input = '')
    {
        if ($input == config('settings.status.open') ||
            $input == config('settings.status.close')) {
            return $this->builder->where('polls.status', $input);
        }

        return $this;
    }
}
