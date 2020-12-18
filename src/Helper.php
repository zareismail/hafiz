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
        return static::has($user, 'tenant_role'); 
    }

    /**
     * Ensure the given user is be a Tenant.
     *  
     * @param  \Illuminate\Database\Eloquent\Model  $user
     * @return static      
     */
    public static function ensureIsTenant($user)
    { 
        return static::ensureHas($user, 'tenant_role');
    }

    /**
     * Determine if the given user is Landlord.
     * 
     * @param  \Illuminate\Database\Eloquent\Model  $user
     * @return boolean      
     */
    public static function isLandlord($user): bool
    {
        return static::has($user, 'landlord_role');
    }

    /**
     * Ensure the given user is be a Landlord.
     *  
     * @param  \Illuminate\Database\Eloquent\Model  $user
     * @return static      
     */
    public static function ensureIsLandlord($user)
    {
        return static::ensureHas($user, 'landlord_role');
    }

    /**
     * Determine if the given user has the given role.
     * 
     * @param   string $role
     * @param  \Illuminate\Database\Eloquent\Model  $user
     * @return boolean      
     */
    public static function has($user, $role): bool
    {
        return ! is_null($user->loadMissing('roles')->roles->find(Registration::option($role)));
    }

    /**
     * Ensure the given user has the given lord.
     *
     * @param   string $role
     * @param  \Illuminate\Database\Eloquent\Model  $user
     * @return static      
     */
    public static function ensureHas($user, $role)
    {
        if(! static::isTenant($user) && $roleId = intval(Registration::option($role))){
            $user->roles()->syncWithoutDetaching($roleId);
        }  

        return new static;
    } 
}
