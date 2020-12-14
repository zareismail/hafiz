<?php

namespace Zareismail\Hafiz;

use Zareismail\NovaContracts\Models\User;
use Zareismail\Bonchaq\Models\BonchaqContract;


class DynamicRelationship
{  
    /**
     * Register the relationships.
     * 
     * @return static
     */
    public static function register()
    {
        static::registerUserRelationships(config('zareismail.user', User::class));
    }

    /**
     * Register the relationsships for the given user model.
     * 
     * @return static
     */
    public static function registerUserRelationships($user)
    { 
        forward_static_call([$user, 'resolveRelationUsing'], 'contracts', function($userModel) {
            return $userModel->hasMany(BonchaqContract::class, 'auth_id');
        }); 

        forward_static_call([$user, 'resolveRelationUsing'], 'activeContracts', function($userModel) {
            return $userModel->contracts()->inProgress();
        }); 
    }
}
