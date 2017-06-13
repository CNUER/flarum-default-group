<?php 
namespace Hyn\DefaultGroup\Listeners;

use Flarum\Event\UserWasActivated;
use Flarum\Event\UserEmailWasChanged;
use Flarum\Core\Group;

use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Contracts\Events\Dispatcher;

class AddDefaultGroup
{
    /**
     * @var SettingsRepository
     */
    protected $settings;

    /**
     * @var int
     */
    protected $defaultGroup;
    protected $cnuGroup;
    
    /**
     * @var string
     */
    protected $cnuGroup_regx;

    public function __construct(SettingsRepositoryInterface $settings) {
        $this->settings = $settings;

        $this->defaultGroup = (int) $this->settings->get('hyn.default_group.group', Group::MEMBER_ID);
        $this->cnuGroup = (int) $this->settings->get('hyn.cnu_group.group', Group::MEMBER_ID);
        $this->cnuGroup = $this->settings->get('hyn.cnuGroup_regx', '@cnu.edu.cn$');
    }

    /**
     * Subscribe to event dispatcher
     * @param Dispatcher $events
     */
    public function subscribe(Dispatcher $events)
    {
        $events->listen(UserWasActivated::class, [$this, 'addGroup']);
        $events->listen(UserEmailWasChanged::class, [$this, 'changeGroup']);
    }

    /**
     * Attaches the default group to the activated user
     * @param UserWasActivated $event
     */
    public function addGroup(UserWasActivated $event) {
        //认证用户组
        if($this->cnuGroup && preg_match('/'.$this->cnuGroup_regx.'/',$event->user->email)) {
            $event->user->groups()->attach($this->cnuGroup);
            return;
        }

        if($this->defaultGroup == Group::MEMBER_ID) {
            return;
        }
        $event->user->groups()->attach($this->defaultGroup);
    }

    /**
     * Attaches the default group to the activated user
     * @param UserWasActivated $event
     */
    public function changeGroup(UserEmailWasChanged $event) {
        if($this->cnuGroup && preg_match('/'.$this->cnuGroup_regx.'/',$event->user->email)) {
            $event->user->groups()->attach($this->cnuGroup);
        }elseif($event->user->groups()->detach($this->cnuGroup)){//成功踢出认证组
            $event->user->groups()->attach($this->defaultGroup);//加入普通组
        }
    }
}