<?php

namespace Zareismail\Hafiz;

use Zareismail\Hafiz\Nova\Registration;

class Helper
{   
    /**
     * Determine if the given user is Tenant.
     * 
     * @param  \Illuminate\Database\Eloquent\Model  $user
     * @return boolean      
     */
    public static function isTenant($user): bool
    {
        return ! is_null($user->loadMissing('roles')->roles->find(Registration::option('tenant_role')));
    }

    /**
     * Ensure the given user is be a Tenant.
     *  
     * @param  \Illuminate\Database\Eloquent\Model  $user
     * @return static      
     */
    public static function ensureIsTenant($user)
    {
        if(! static::isTenant($user) && $roleId = intval(Registration::option('tenant_role'))){
            $user->roles()->syncWithoutDetaching($roleId);
        }  

        return new static;
    }
}
