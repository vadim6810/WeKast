<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Presentation extends Model
{
    /**
     * Хелпер, задающий пользователя презентации
     * @param User $user
     */
    public function setUser(User $user) {
        $this->user_id = $user->id;
    }
}
