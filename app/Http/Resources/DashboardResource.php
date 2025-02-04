<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

class DashboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'message' => 'Welcome to Dashboard!',
            'access' => $this->getAccessMessage(),
            'user' => [
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email,
                'roles' => $this->roles->pluck('name')
            ]
        ];
    }

    /**
     * Get the access message based on user roles.
     *
     * @return string
     */
    private function getAccessMessage(): string
    {
        $accessMessages = [];

        if (Gate::allows('isAdmin')) {
            $accessMessages[] = 'You have admin access';
        }
        if (Gate::allows('isManager')) {
            $accessMessages[] = 'You have manager access';
        }
        if (Gate::allows('isUser')) {
            $accessMessages[] = 'You have user access';
        }

        return !empty($accessMessages) ? implode(', ', $accessMessages) : '';
    }
}
