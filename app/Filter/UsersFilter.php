<?php

namespace App\Filter;

use App\QueryFilter;

class UsersFilter extends QueryFilter
{
    public function input()
    {
        return parent::filters();
    }

    public function name($input = '')
    {
        if (! $input) {
            return $this;
        }

        return $this->builder->where('name', 'LIKE', '%' . $input . '%');
    }

    public function email($input = '')
    {
        if (! $input) {
            return $this;
        }

        return $this->builder->where('email', 'LIKE', '%' . $input . '%');
    }

    public function chatwork($input = '')
    {
        if (! $input) {
            return $this;
        }

        return $this->builder->where('chatwork_id', 'LIKE', '%' . $input . '%');
    }

    public function gender($input = '')
    {
        $config = config('settings.gender_constant');

        if ($input == $config['male'] ||
            $input == $config['female']) {
            return $this->builder->where('gender', $input);
        }

        if ($input == $config['other']) {
            return $this->builder->where('gender', '<>', $config['male'])
                ->where('gender', '<>', $config['female']);
        }

        return $this;
    }
}
