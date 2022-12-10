<?php


namespace App\Repositories\Dashboard;


use App\Enums\ResponseStatusCode\AResponseStatusCode;
use App\Enums\UserTypes\UserType;
use App\Mail\SendMail;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ConsumerRepository
{
    /**
     * @var User
     */
    private $model;

    /**
     * ConsumerRepository constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->model = $user;
    }

    /**
     * List all consumers.
     * @return Builder[]|Collection
     */
    public function index()
    {
        return $this->model::query()->select(['id', 'name', 'mobile', 'email', 'activation'])->where('type_id', UserType::CONSUMER)->get();
    }

    /**
     * View consumer by id.
     * @param $id
     * @return Builder|Model|object|null
     */
    public function view($id)
    {
        return $this->model::query()->select(['users.id', 'users.created_at as register_date', 'users.name', 'users.mobile', 'users.email', 'cities.name_en as city_name', 'addresses.address'])
            ->leftJoin('addresses', 'addresses.user_id', '=', 'users.id')
            ->leftJoin('cities', 'cities.id', '=', 'addresses.city_id')
            ->where('users.id', $id)->first();
    }

    /**
     * change activation status for consumer.
     * @param $data
     * @return bool
     */
    public function changeConsumerStatus($data): bool
    {
        $user = $this->getUser($data['id']);
        $user->activation = $data['status'];
        if ($user->save())
            return true;

        return false;
    }

    /**
     * Update consumer.
     * @param $data
     * @return bool
     */
    public function update($data): bool
    {
        $user = $this->getUser($data['id']);
        $user->email = $data['email'];
        if ($data['password']) {
            $user->password = bcrypt($data['password']);
        }

        if ($user->save())
            return true;

        return false;
    }

    /**
     * Get user object.
     * @param $id
     * @return Builder|Model|object|null
     */
    private function getUser($id)
    {
        return $this->model::query()->where('users.id', $id)->first();
    }

    /**
     * Send email.
     * @param $data
     */
    public function sendEmail($data)
    {
        Mail::to($data['email'])->send(new SendMail($data));
    }
}
