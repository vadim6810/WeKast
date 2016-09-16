<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Presentation extends Model
{

    /**
     * Атрибуты, которые видны при выдаче в JSON
     * @var array
     */
    protected $visible = ['id', 'name', 'hash'];

    /**
     * Хелпер, задающий пользователя презентации
     * @param User $user
     */
    public function setUser(User $user) {
        $this->user_id = $user->id;
    }

    /**
     * Проверяет, приналдежит ли презентация заданному пользователю
     * @param User $user
     * @return bool
     */
    public function isBellongs(User $user) {
        if ($this->user_id == $user->id) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Возвращает список презентаций заданного пользователя
     * @param User $user
     * @param int $page
     * @return Presentation[]
     */
    static public function byUser(User $user, $page = 0) {
        return self::where('user_id', $user->id)->take(50)->skip(50 * $page)->get();
    }

    static public function byUserName(User $user, $name) {
        return self::where('user_id', $user->id)->where('name', $name)->first();
    }
}
