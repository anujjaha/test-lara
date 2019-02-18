<?php namespace FTX\Services\Access;

/**
 * Class Access
 *
 * @author Justin Bevan justin@smokerschoiceusa.com
 * @package FTX\Services\Access
 */

use DateTime, DateTimeZone;
use FTX\Exceptions\CustomHttpResponseException;
use FTX\Models\Access\Accounts\Account;
use FTX\Models\Access\Customer\Customer;
use FTX\Models\Access\Employee\Employee;
use FTX\Models\Access\User\User;
use FTX\Models\Manufacturer\Access\ManufacturerUser\ManufacturerUser;
use FTX\Models\POS\PricebookZone\PricebookZone;
use FTX\Models\Signage\Locations\Location;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use FTX\Models\Loyalty\CustomerBalanceHistory\CustomerBalanceHistory;
use FTX\Models\Manufacturer\Access\ManufacturerAccount\ManufacturerAccount;

class Access
{
    /**
     * Laravel application
     *
     * @var \Illuminate\Foundation\Application
     */
    public $app;

    /**
     * Guard
     *
     * @var string
     */
    protected $guard;

    private static $accountTempCache;

    /**
     * Create a new confide instance.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Set Guard
     *
     * @param $guard
     * @return $this
     */
    public function setGuard($guard)
    {
        $this->guard = $guard;
        return $this;
    }

    /**
     * Get Guard
     *
     * @param Customer|User|Employee|null $model
     * @return string
     */
    public function getGuard($model = null)
    {
        if(!is_null($model) && is_object($model))
        {
            if($model instanceof \FTX\Models\Access\Customer\Customer)
            {
                $this->guard = 'customer';

                return $this->guard;
            }
        }

        if(!is_null($model) && is_object($model))
        {
            if($model instanceof \FTX\Models\Manufacturer\Access\ManufacturerUser\ManufacturerUser)
            {
                $this->guard = 'manufacturer';

                return $this->guard;
            }
        }

        if(isset($this->guard) && $this->guard != '')
        {
            return $this->guard;
        }

        if(auth('customer')->check())
        {
            $this->guard = 'customer';
        }
        else if(auth('employee')->check())
        {
            $this->guard = 'employee';
        }
        else if(auth('manufacturer')->check())
        {
            $this->guard = 'manufacturer';
        }
        else if($this->isLoyaltyManufacturerDomain())
        {
            $this->guard = 'manufacturer';
        }
        else
        {
            $this->guard = 'web';
        }

        return $this->guard;
    }

    /**
     * Get the Associated User
     *
     * @return User|Customer|Employee
     */
    public function user()
    {
        return auth($this->getGuard())->user();
    }

    /**
     * Get Account
     *
     * @return mixed|Account
     */
    public function account()
    {
        if(isset($this->user()->account_id))
        {
            // Generate Unique Cache Key for User
            $cacheKey   = access()->getAccountCacheKey();
            $globalKey  = 'accountCache';

            if(isset($GLOBALS[$globalKey]) && !empty($GLOBALS[$globalKey]))
            {
                return $GLOBALS[$globalKey];
            }
            else if(access()->hasCache($cacheKey))
            {
                $model = access()->getCache($cacheKey);

                if($model instanceof Account)
                {
                    $cacheRes = access()->getCache($cacheKey);

                    $GLOBALS[$globalKey] = $cacheRes;

                    return $cacheRes;
                }

                $account = $this->user()->account()->first();
                access()->putCache($cacheKey, $account, 100);

                $GLOBALS[$globalKey] = $account;

                return $account;
            }
            else
            {
                $account = $this->user()->account()->first();
                access()->putCache($cacheKey, $account, 100);

                $GLOBALS[$globalKey] = $account;

                return $account;
            }
        }

        if(isset($this->user()->manufacturer_account))
        {
            return $this->user()->manufacturer_account;
        }
        
        return null;
    }

    /**
     * Get Manufacturer Account
     *
     * @return object
     */
    public function manufacturerAccount()
    {
        return new ManufacturerAccount;
    }

    /**
     * Get Account Package
     *
     * @return mixed
     */
    public function locations()
    {
        if(isset($this->user()->all_locations) && $this->user()->all_locations == 1)
        {
            return $this->account()->locations;
        }

        if(isset($this->user()->locations))
        {
            return $this->user()->locations;
        }
    }

    /**
     * Get Account Locations
     *
     * @param int|string $accountId
     * @return Collection
     */
    public function getAccountLocations($accountId)
    {
        $locationModel = new Location();

        return $locationModel->select(
            'id',
            'name',
            'store_number'
        )
        ->where('account_id', $accountId)
        ->get()
        ->mapWithKeys(function(Location $location)
        {
            return [$location->id => $location->getStoreName()];
        });
    }

    /**
     * Get Account Package
     *
     * @return mixed
     */
    public function package()
    {
        return $this->account()->accountPackage;
    }

    /**
     * Get the currently authenticated user's id
     *
     * @return mixed
     */
    public function id()
    {
        return auth()->id();
    }

    /**
     * Checks if the current user has a Role by its name or id
     *
     * @param  string $role Role name.
     * @return bool
     */
    public function hasRole($role)
    {
        if($user = $this->user())
        {
            return $user->hasRole($role);
        }

        return false;
    }

    /**
     * Checks if the user has either one or more, or all of an array of roles
     *
     * @param $roles
     * @param bool $needsAll
     * @return bool
     */
    public function hasRoles($roles, $needsAll = false)
    {
        if($user = $this->user())
        {
            //If not an array, make a one item array
            if(!is_array($roles))
            {
                $roles = array($roles);
            }

            return $user->hasRoles($roles, $needsAll);
        }

        return false;
    }

    /**
     * Check if the current user has a permission by its name or id
     *
     * @param  string $permission Permission name or id.
     * @return bool
     */
    public function allow($permission)
    {
        if($user = $this->user())
        {
            if(method_exists($user, 'allow'))
            {
                return $user->allow($permission);
            }
        }

        return false;
    }

    /**
     * Check if the current user has a permission by group alias
     *
     * @param string $alias.
     * @return bool
     */
    public function hasGroup($alias)
    {
        if(!$this->isEmployee())
        {
            if($user = $this->user())
            {
                return $user->hasGroup($alias);
            }

        }

        return false;
    }

    /**
     * Check if Account Has Group
     *
     * @param $account
     * @param $alias
     * @return mixed
     */
    public function accountHasGroup($account, $alias)
    {
        return $account ? $account->hasGroup($alias) : false;
    }

    /**
     * Check an array of permissions and whether or not all are required to continue
     *
     * @param $permissions
     * @param bool $needsAll
     * @return bool
     */
    public function allowMultiple($permissions, $needsAll = false)
    {
        if($user = $this->user())
        {
            //If not an array, make a one item array
            if(!is_array($permissions))
            {
                $permissions = array($permissions);
            }

            return $user->allowMultiple($permissions, $needsAll);
        }

        return false;
    }

    /**
     * this will check if permission is avilable or not
     *
     * @param  $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        return $this->allow($permission);
    }

    /**
     * This function is used to check multiple permissions
     *
     * @param  $permissions
     * @param  $needsAll
     * @return bool
     */
    public function hasPermissions($permissions, $needsAll = false)
    {
        return $this->allowMultiple($permissions, $needsAll);
    }

    /**
     * Is Super Admin
     *
     * @return mixed
     */
    public function isSuperAdmin()
    {
        if(isset($GLOBALS['isSuperAdmin']) && $GLOBALS['isSuperAdmin'] == 1)
        {
            return true;
        }

        $isSuperAdmin = access()->hasPermission('view-backend');

        if($isSuperAdmin)
        {
            $GLOBALS['isSuperAdmin'] = 1;
        }

        return $isSuperAdmin;
    }

    /**
     * Is API User
     *
     * @return bool
     */
    public function isAPI()
    {
        if(isset($GLOBALS['isAPI']) && $GLOBALS['isAPI'] == true)
        {
            return true;
        }

        $isAPIRoute = strpos(request()->url(), '/api/') !== false;

        $GLOBALS['isAPI'] = $isAPIRoute;

        return $isAPIRoute;
    }

    /**
     * Has Sub Account
     *
     * @param Account|int $account
     * @return bool
     */
    public function hasSubAccount($account)
    {
        $accountId = $account instanceof Account ? $account->id : $account;

        if(hasher()->isValid($accountId))
        {
            $account = (new Account())->where('id', $accountId)
                ->where('parent_id', access()->account()->id)
                ->count();

            return is_numeric($account) && $account > 0 ? true : false;
        }

        return false;
    }

    /**
     * Is User
     *
     * @return mixed
     */
    public function isUser()
    {
        return !access()->isEmployee();
    }

    /**
     * Is Manufacturer
     *
     * @return bool
     */
    public function isManufacturer()
    {
        return $this->user() instanceof ManufacturerUser;
    }

    /**
     * Can Own Accounts
     *
     * @param null $account
     * @return bool
     */
    public function canOwnAccounts($account = null)
    {
        if($account == null)
        {
            $account = $this->account();
        }

        return ($account && isset($account->can_own_accounts) && $account->can_own_accounts == 1) ? true : false;
    }

    /**
     * Get Sub Accounts
     *
     * @return mixed
     */
    public function subAccounts()
    {
        return $this->account()->sub_accounts()->get();
    }

    /**
     * Current And Sub Accounts
     *
     * @param Access $account
     * @param bool $hashed
     * @return mixed
     */
    public function currentAndSubAccounts($account = null, $hashed = false)
    {
        if($account === null)
        {
            $subAccounts = $this->subAccounts();
            $subAccounts->prepend(access()->account());

            if($hashed)
            {
                return $subAccounts->map(function($account)
                {
                    $account->hashId();

                    return $account;
                });
            }

            return $subAccounts;
        }
        else
        {
            $subAccounts = $account->sub_accounts()->get();
            $subAccounts->prepend($account);

            if($hashed)
            {
                return $subAccounts->map(function($account)
                {
                    $account->hashId();

                    return $account;
                });
            }

            return $subAccounts;
        }
    }

    /**
     * Current and Sub-Account List
     *
     * @param bool|true $includeEmpty
     * @param Access $account
     * @return array
     */
    public function currentAndSubAccountList($includeEmpty = true, $account = null)
    {
        $return = $includeEmpty ? ['' => 'Select Account'] : [];

        $matches = $this->currentAndSubAccounts($account);

        if($matches->count() > 0)
        {
            foreach($matches as $match)
            {
                $return[$match->id] = $match->company;
            }

            return $return;
        }

        return $return;
    }

    /**
     * Model Has Lock
     *
     * @param $model
     * @return bool
     */
    public function modelHasLock($model)
    {
        return ((isset($model->is_locked) && $model->is_locked && !$this->modelLockAccess()) ? true : false);
    }

    /**
     * Model Lock Access
     *
     * @return bool
     */
    public function modelLockAccess()
    {
        return (((access()->isSuperAdmin() || access()->canOwnAccounts()) || access()->hasPermission('edit-lock-content')) ? true : false);
    }

    /**
     * Account Parent
     *
     * @return mixed
     */
    public function accountParent()
    {
        return $this->account()->accountParent;
    }

    /**
     * Is Sub Account
     *
     * @return bool
     */
    public function isSubAccount()
    {
        if(isset($this->user()->account_id))
        {
            return !$this->isSuperAdmin() && access()->account()->parent_id != null;
        }

        return false;
    }

    /**
     * Model Parent Account
     *
     * @param $model
     * @return mixed
     */
    public function modelParentAccount($model)
    {
        return isset($model->account->accountParent) ? $model->account->accountParent : false;
    }

    /**
     * All Accounts
     *
     * @param bool|true $includeEmpty
     * @param bool|true $includeSuperAdmin
     * @return array
     */
    public function allAccounts($includeEmpty = true, $includeSuperAdmin = false)
    {
        $connection = \DB::connection()->getName();

        // Returning query result from static variable.
        if(!empty(self::$accountTempCache) && isset(self::$accountTempCache[$connection][$includeEmpty >> 0][$includeSuperAdmin >> 0]))
        {
            return self::$accountTempCache[$connection][$includeEmpty >> 0][$includeSuperAdmin >> 0];
        }

        $return     = $includeEmpty ? ['' => 'Select Account'] : [];
        $account    = $this->account();
        $matches    = $account->where(function($query) use($includeSuperAdmin)
        {
            if($includeSuperAdmin)
            {
                $query->where('id', '!=', 1);
            }
        })
        ->orderBy('company')
        ->get();

        if(!$matches->count())
        {
            $matches = \DB::table('accounts')->where(function($query) use($includeSuperAdmin)
            {
                if($includeSuperAdmin)
                {
                    $query->where('id', '!=', 1);
                }
            })
            ->orderBy('company')
            ->get();
        }

        if($matches->count() > 0)
        {
            foreach($matches as $match)
            {
                $return[$match->id] = $match->company;
            }
        }

        // Caching query into static variable.
        self::$accountTempCache[$connection][$includeEmpty >> 0][$includeSuperAdmin >> 0] = $return;

        return $return;
    }

    /**
     * Loyalty Accounts
     *
     * @param arary $ignoreList
     * @return Account[]|Builder[]|Collection
     */
    public function loyaltyAccounts($ignoreList = array())
    {
        $query =  (new Account())->where('slug', '!=', '')
            ->where('id', '!=', 1)
            ->whereHas('loyalty_options', function(Builder $query)
            {
                $query->where('chain_active', '=', 1);
            });

        if(isset($ignoreList) && count($ignoreList))
        {
            $query->whereNotIn('id', $ignoreList);
        }

        return $query->get();
    }

    /**
     * All Manufacturer Accounts
     *
     * @param bool|true $includeEmpty
     * @return array
     */
    public function allManufacturerAccounts($includeEmpty = true)
    {
        $connection = \DB::connection()->getName();

        // Returning query result from static variable.
        if(!empty(self::$accountTempCache) && isset(self::$accountTempCache[$connection][$includeEmpty >> 0]))
        {
            return self::$accountTempCache[$connection][$includeEmpty >> 0];
        }

        $return     = $includeEmpty ? ['' => 'Select Account'] : [];
        $account    = $this->manufacturerAccount();

        /** @var Collection $matches */
        $matches = $account->query()
            ->orderBy('name')
            ->get();

        if(!$matches->count())
        {
            $matches = (new ManufacturerAccount())->newQuery()
                ->orderBy('name')
                ->get();
        }

        if($matches->count() > 0)
        {
            foreach($matches as $match)
            {
                $return[$match->id] = $match->name;
            }
        }

        // Caching query into static variable.
        self::$accountTempCache[$connection][$includeEmpty >> 0] = $return;

        return $return;
    }

    /**
     * Check if Has Cache
     *
     * @param $cacheKey
     * @return mixed
     * @throws \Exception
     */
    public function hasCache($cacheKey)
    {
        return cache()->has($cacheKey);
    }

    /**
     * Get Cache By Key
     *
     * @param $cacheKey
     * @return mixed
     * @throws \Exception
     */
    public function getCache($cacheKey)
    {
        return cache()->get($cacheKey);
    }

    /**
     * Put Cache By Key
     *
     * @param $cacheKey
     * @param $items
     * @param int $minutes
     * @throws \Exception
     */
    public function putCache($cacheKey, $items, $minutes = 10)
    {
        cache()->put($cacheKey, $items, $minutes);
    }

    /**
     * Cache Forever
     *
     * @param string $cacheKey
     * @param string $value
     */
    public function foreverCache($cacheKey, $value)
    {
        cache()->forever($cacheKey, $value);
    }

    /**
     * Forget Cache
     *
     * @param string $cacheKey
     */
    public function forgetCache($cacheKey)
    {
        cache()->forget($cacheKey);
    }

    /**
     * Flush all Cache
     *
     */
    public function flushAllCache()
    {
        cache()->flush();
    }

    /**
     * Flush All User Cache
     *
     * @param object|null $user
     * @return bool
     */
    public function flushAllUserCache($user = null)
    {
        $user = $user ? $user : access()->user();

        /**
         * Flush permissions
         *
         * reference : AdminUserTable::getHierarchicalPermissions()
         */
        if($user instanceof User)
        {
            $user->flushUserPermissionCache();
        }

        /**
         * Flush User Cache
         * Cached in this function \Repositories\Admin\POS\Message\EloquentMessageRepository\getUserSelectDataByAccountId
         */
        cache()->forget('employee/messages/getUsers');

        return true;
    }

    /**
     * Get Default Timezone
     *
     * @return string
     */
    public function getDefaultTimezone()
    {
        return 'America/New_York';
    }

    /**
     * Get Account Timezone
     *
     * @param object $account
     * @return string
     */
    public function getAccountTimezone($account = null)
    {
        $model      = $account;
        $cacheKey   = 'account/ ' . (isset($account->id) ? $account->id : 'null') . '/timezone/tzCache';

        if(cache()->has($cacheKey))
        {
            $timeZone = cache()->get($cacheKey);

            if($timeZone && $timeZone !== '')
            {
                return $timeZone;
            }
        }

        if(!$account)
        {
            if(isset($this->account()->config->timezone) && !empty($this->account()->config->timezone))
            {
                $timezone = $this->account()->config->timezone;

                cache()->put($cacheKey, $timezone, 30);

                return $timezone;
            }
        }

        if((is_string($account) || is_numeric($account)) && $account != '')
        {
            try
            {
                $id     = $account;
                $model  = Account::with('config')->find($id);
            }
            catch(\Exception $e)
            {
                return $this->getDefaultTimezone();
            }
        }

        if(isset($model->config->timezone) && !empty($model->config->timezone))
        {
            $timezone = $model->config->timezone;

            cache()->put($cacheKey, $timezone, 30);

            return $timezone;
        }

        $timezone = $this->getDefaultTimezone();

        cache()->put($cacheKey, $timezone, 30);

        return $timezone;
    }

    /**
     * Get Selected Account Timezone
     *
     * @return string
     */
    public function getSelectedAccountTimezone()
    {
        return $this->getAccountTimezone($this->getSelectedAccount());
    }

    /**
     * Get Account Start Date
     *
     * @param null $account
     * @param string $format
     * @return string
     */
    public function getAccountStartDate($account = null, $format = 'd-m-Y')
    {
        if($account)
        {
            $timeZone = $this->getAccountTimezone($account);
        }
        else
        {
            $timeZone = $this->getDefaultTimezone();
        }

        $defaultDateTime = new DateTime('@' . time());

        $defaultDateTime->setTimezone(new DateTimeZone($timeZone));

        return $defaultDateTime->format($format);
    }

    /**
     * Check Account Has Permissions
     *
     * @param $account
     * @param $nameOrId
     * @return bool
     */
    public function accountHasPermission($account, $nameOrId)
    {
        return $account->hasPermission($nameOrId);
    }

    /**
     * Determine whether the user can access the item.
     *
     * @param $item
     * @return bool
     * @throws CustomHttpResponseException
     */
    public function authorizeItem($item)
    {
        if($this->isSuperAdmin())
        {
            return true;
        }

        if($this->canOwnAccounts())
        {
            // if logged in user in parent account itself
            if($item->account_id == $this->user()->account_id)
            {
                return true;
            }

            // if model is of any sub account of logged in parent account
            $subAccounts = $this->subAccounts()->pluck('id')->toArray();

            if(in_array($item->account_id, $subAccounts))
            {
                return true;
            }

            throw new CustomHttpResponseException;

        }
        else
        {
            return $item->account_id == $this->user()->account_id;
        }
    }

    /**
     * Get Base Domain
     *
     * @param string $protocol
     * @return string
     */
    public function getBaseDomain($protocol = 'http')
    {
        $siteUrl    = request()->fullUrl();
        $domains    = [
            'staging.goftx.com',
            'move.goftx.com',
            'dev.goftx.com',
            'petdemo.goftx.com'
        ];

        foreach($domains as $domain)
        {
            if(strpos($siteUrl, $domain) !== false)
            {
                return $protocol . '://' . $domain;
            }
        }

        return 'http://controlcenter.fastraxpos.com';
    }

	/**
	 * Show Loyalty App
	 *
	 * @return bool
	 */
	public function showLoyaltyApp($domain = 'admin')
	{
        switch($domain)
        {
            case 'admin':

                return !$this->isSuperAdmin() && $this->isLoyaltyAdminDomain();

            case 'customer':

                return !$this->isSuperAdmin() && $this->isLoyaltyCustomerDomain();

            case 'manufacturer':

                return !$this->isSuperAdmin() && $this->isLoyaltyManufacturerDomain();

            default:

                return false;
        }
	}

	/**
	 * Is Admin Loyalty
	 *
	 * @return bool
	 */
    public function isLoyaltyAdminDomain()
    {
	    $configUrl      = config('app.adminLoyaltyUrl');
	    $currentUrl     = request()->fullUrl();
	    $configHost     = common()->getHostFromUrl($configUrl);
	    $currentHost    = common()->getHostFromUrl($currentUrl);

	    return $configHost == $currentHost;
    }

    /**
     * Is Customer Loyalty
     *
     * @returns bool
     */
    public function isLoyaltyCustomerDomain()
    {
        $configUrl      = config('app.loyaltyUrl');
        $currentUrl     = request()->fullUrl();
        $configHost     = common()->getHostFromUrl($configUrl);
        $currentHost    = common()->getHostFromUrl($currentUrl);

        return $configHost == $currentHost;
    }

    /**
     * Is Manufacturer Loyalty
     *
     * @returns bool
     */
    public function isLoyaltyManufacturerDomain()
    {
        $configUrl      = config('app.manufacturerUrl');
        $currentUrl     = request()->fullUrl();
        $configHost     = common()->getHostFromUrl($configUrl);
        $currentHost    = common()->getHostFromUrl($currentUrl);

        return $configHost == $currentHost;
    }

    /**
     * Check Non-Staging Sites
     *
     * @param bool $includeStaging
     * @return bool|mixed
     */
    public function checkNonStagingSites($includeStaging = false)
    {
        $siteUrl    = request()->fullUrl();
        $siteHost   = common()->getHostFromUrl($siteUrl);
        $siteParts  = [
            'controlcenter.fastraxpos.com',
        ];

        if($includeStaging)
        {
            $siteParts = array_merge($siteParts, [
                'staging.goftx.com',
                'move.goftx.com',
                'dev.goftx.com',
                'petdemo.goftx.com'
            ]);
        }

        foreach($siteParts as $siteDomain)
        {
            if($siteDomain == $siteHost)
            {
	            return true;
            }
        }

        return false;
    }

    /**
     * Check Non-Staging Sites (Loyalty)
     *
     * @param bool $includeStaging
     * @return bool|mixed
     */
    public function checkNonStagingLoyaltySites($includeStaging = false)
    {
        $siteUrl    = request()->fullUrl();
        $siteHost   = common()->getHostFromUrl($siteUrl);
        $siteParts  = [
            'loyalnsave.com',
        ];

        if($includeStaging)
        {
            $siteParts = array_merge($siteParts, [
                'dev.loyalnsave.com',
            ]);
        }

        foreach($siteParts as $siteDomain)
        {
            if($siteDomain == $siteHost)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Get Default Pricebook Zone
     *
     * @param bool $account
     * @return mixed|PricebookZone
     */
    public function getDefaultPricebookZone($account = false)
    {
        $account = $account ? $account : access()->account();

	    if(is_string($account) || is_numeric($account))
	    {
		    $decoded = hasher()->decode($account);

		    if(is_numeric($decoded))
		    {
			    $account = Account::find($account);
		    }
	    }

        $cacheKey = 'pos/pricebook/zone/pricebook-zone-default-' . $account->id;

        if(access()->hasCache($cacheKey))
        {
            $model = access()->getCache($cacheKey);

            if($model instanceof PricebookZone)
            {
                return $model;
            }
        }

        $pricebookZoneModel = new PricebookZone();

        $pricebookZone = $pricebookZoneModel->where([
            'account_id'    => $account->getOriginal('id'),
            'default_flag'  => 1
        ])->first();

        access()->putCache($cacheKey, $pricebookZone, 100);
        return $pricebookZone;
    }

    /**
     * Find Default Pricebook Zone Options
     *
     * @param bool|Account $account
     * @return array|bool
     */
    public function getDefaultPricebookZoneObject($account = false)
    {
        $account = $account ? $account : access()->account();

        if(is_string($account) || is_numeric($account))
        {
            $decoded = hasher()->decode($account);

            if(is_numeric($decoded))
            {
                $account = Account::find($account);
            }
        }

        $cacheKey = 'pos/pricebook/zone/pricebook-zone-default-object-' . $account->id;

        if(access()->hasCache($cacheKey))
        {
            $model = access()->getCache($cacheKey);

            if($model instanceof PricebookZone)
            {
                return $model;
            }
        }

        $pricebookZoneModel = new PricebookZone();

        $record = $pricebookZoneModel->where('account_id', '=', $account->getOriginal('id'))->where('default_flag', '=', 1)->first();

        if($record)
        {
            $result = [];

            $result['zone']                    = $record['id'];
            $result['refName']                 = $record['name'];
            $result['refId']                   = $record['id'];
            $result['type']                    = 'standard';
            $result['cost']                    = 0;
            $result['default']                 = true;
            $result['price_option']['price']   = 0;
            $result['price_option']['inv_qty'] = 1;

            access()->putCache($cacheKey, array($result), 100);
            return array($result);
        }

        return [];
    }

    /**
     * Clear Pricebook Zone Cache
     *
     * @param bool $account
     * @return mixed
     * @throws \Exception
     */
    public function clearPricebookZoneCache($account = false)
    {
        $account    = $account ? $account : access()->account();
        $cacheKey   = 'pos/pricebook/zone/pricebook-zone-default-' . is_numeric($account) ? $account : $account->id;

        return cache()->forget($cacheKey);
    }

    /**
     * Get Account By ID
     *
     * @param  integer $accountId
     * @return Account|bool
     */
    public function getAccountById($accountId)
    {
        $account = Account::find($accountId);

        return !empty($account) ? $account : false;
    }

    /**
     * Get User Key
     *
     * @return string
     */
    public function getUserKey()
    {
        return access()->isSuperAdmin() ? 'admin' : (access()->isEmployee() ? 'employee' : (access()->isCustomer() ? 'customer' : 'client'));
    }

    /**
     * Get Default Account ID
     *
     * @return int|mixed
     */
    public function getDefaultAccountId()
    {
        if(!access()->isSuperAdmin() && isset(access()->account()->id))
        {
            return access()->account()->id;
        }

        $config = config('access.defaultAccount');

        return $config ? $config : 34;
    }   

    /**
     * Get Default Manufacturer Account ID
     *
     * @return int|mixed
     */
    public function getDefaultManufacturerAccountId()
    {
        $config = config('access.defaultManufacturerAccount');

        return $config ? $config : 1;
    }

    /**
     * Get Selected Account ID
     *
     * @param int|null $default
     * @return int|string
     */
    public function getSelectedAccountId($default = null)
    {
        if(!$this->isManufacturer())
        {
            $session = session()->get('currentModelAccount');

            if($session === 'all')
            {
                return ($default ? $default : access()->getDefaultAccountId());
            }

            return $session ? (is_numeric($session) ? $session : hasher()->decode($session)) : ($default ? $default : access()->getDefaultAccountId());
        }
        else
        {
            $session = session()->get('currentModelAccount');

            if($session === 'all')
            {
                return ($default ? $default : access()->getDefaultManufacturerAccountId());
            }

            return $session ? (is_numeric($session) ? $session : hasher()->decode($session)) : ($default ? $default : access()->getDefaultManufacturerAccountId());
        }
    }

    /**
     * Set Selected Account
     *
     * @param string|int $id
     */
    public function setSelectedAccount($id)
    {
        session()->put(['currentModelAccount' => $id]);
    }

    /**
     * Get Selected Account
     *
     * @param null|mixed $default
     * @return Account|Access
     */
    public function getSelectedAccount($default = null)
    {
        $accountId = $this->getSelectedAccountId($default);
        $cacheKey   = 'account/' . $accountId . '/selected';

        if(cache()->has($cacheKey))
        {
            $cacheRes = cache()->get($cacheKey);

            if($cacheRes instanceof Account)
            {
                return $cacheRes;
            }
        }

        $account = Account::find($accountId);

        cache()->put($cacheKey, $account, 30);

        return $account;
    }

    /**
     * Get ManufacturerSelected Account
     *
     * @param null|mixed $default
     * @return ManufacturerAccount|Access
     */
    public function getManufacturerSelectedAccount($default = null)
    {
        $accountId  = config('access.defaultManufacturerAccount');
        $cacheKey   = 'manufacturer-account/' . $accountId . '/selected';

        if(cache()->has($cacheKey))
        {
            $cacheRes = cache()->get($cacheKey);

            if($cacheRes instanceof ManufacturerAccount)
            {
                return $cacheRes;
            }
        }

        $account = ManufacturerAccount::find($accountId);

        cache()->put($cacheKey, $account, 30);

        return $account;
    }

    /**
     * Get Selected Employee Location ID
     *
     * @return bool
     */
    public function getSelectedEmployeeLocationId()
    {
        if($this->user() instanceof Employee && method_exists($this->user(), 'getSelectedLocationId'))
        {
            return $this->user()->getSelectedLocationId();
        }

        return false;
    }

    /**
     * Get Selected Employee Location
     *
     * @return Location|Access|bool
     */
    public function getSelectedEmployeeLocation()
    {
        $selectedLocationId = $this->getSelectedEmployeeLocationId();

        if($selectedLocationId)
        {
            $locationModel  = new Location();
            $location       = $locationModel->find($selectedLocationId);

            return $location;
        }

        return false;
    }

    /**
     * Get Locations by employee Id
     *
     * @param  string $employeeId
     * @return array
     */
    public function getLocationByEmployeeId($employeeId = NULL)
    {
        $default = [0 => 'Select Location'];

        if(empty($employeeId))
        {
            return [] + $default;
        }

        $employee = Employee::find($employeeId);

        if(isset($employee->id) && $employee instanceof Employee)
        {
            $employeeLocations  = $employee->getEmployeeLocations($employee);
            $locations          = $employeeLocations->sortBy('store_number')->mapWithKeys(function(Location $location)
            {
                return [$location->getHashedId() => $location->getStoreName()];
            })->toArray();

            return $locations + $default;
        }

        return [] + $default;
    }

    /**
     * Check Employee Setting
     *
     * @param $setting
     * @return bool
     */
    public function checkEmployeeSetting($setting)
    {
        $selectedLocation = method_exists($this->user(), 'getSelectedLocationId') ? $this->user()->getSelectedLocationId() : false;

        if($selectedLocation && method_exists($this->user(), 'getSetting'))
        {
            return $this->user()->getSetting($selectedLocation, 'dashboard_settings', $setting);
        }

        return false;
    }

    /**
     * Get Account Cache Key
     *
     * @return string
     */
    public function getAccountCacheKey()
    {
        $user       = $this->user();
        $userId     = $user->id;
        $accountId  = $user->account_id;

        return "account/$accountId/userAccount_" . $userId;
    }

    /**
     * Get Manufacturer Account Cache Key
     *
     * @return string
     */
    public function getManufacturerAccountCacheKey()
    {
        $user       = $this->user();
        $userId     = $user->id;
        $accountId  = $user->account_id;

        return "manufacturer-account/$accountId/userAccount_" . $userId;
    }

    /**
     * Get User Account Permission Cache Key
     *
     * @return string
     */
    public function getUserAccountPermissionCacheKey()
    {
        $user       = $this->user();
        $userId     = $user->id;
        $accountId  = $user->account_id;

        return "account/$accountId/permission/userAccountPermission_" . $accountId . "_" . $userId;
    }

    /**
     * Get User Permission Cache Key
     *
     * @return string
     */
    public function getUserPermissionCacheKey()
    {
        $user       = $this->user();
        $userId     = $user->id;
        $accountId  = $user->account_id;

        return "account/$accountId/permission/userPermission_" . $accountId . "_" . $userId;
    }

    /**
     * Is Employee
     *
     * @return boolean
     */
    public function isEmployee()
    {
        if($this->user() instanceof Employee)
        {
            return true;
        }

        return false;
    }

    /**
     * Is Customer
     *
     * @return boolean
     */
    public function isCustomer()
    {
        if($this->user() instanceof Customer)
        {
            return true;
        }

        return false;
    }

    /**
     * Get Total ReferralBonus
     *
     * @param int $accountId
     * @param int $customerId
     * @return int
     */
    public function getTotalReferralBonus($accountId = null, $customerId = null)
    {
        if(isset($accountId) && isset($customerId))
        {
            return CustomerBalanceHistory::where([
                'account_id'    => $accountId,
                'customer_id'   => $customerId,
                'bonus_type'    => 'ReferralPoints'
                ])->sum('balance_added');
        }

        return 0;
    }
}
