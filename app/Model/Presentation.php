<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Presentation extends Model
{

    /**
     * Атрибуты, которые видны при выдаче в JSON
     * @var array
     */
    protected $visible = ['id', 'name'];

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
     * @return Presentation[]
     */
    static public function byUser(User $user) {
        return self::where('user_id', $user->id)->get();
    }
}
