import SettingsModal from 'flarum/components/SettingsModal';
import Group from 'flarum/models/Group';
import SelectDropdown from 'flarum/components/SelectDropdown';

export default class DefaultGroupSettingsModal extends SettingsModal {
    className() {
        return 'DefaultGroupSettingsModal Modal--small';
    }

    title() {
        return 'Default group Settings';
    }

    form() {
        this.defaultGroup = this.setting('hyn.default_group.group') || Group.MEMBER_ID;
        this.cnuGroup = this.setting('hyn.cnu_group.group') || Group.MEMBER_ID;
        this.cnuGroupRegx = this.setting('hyn.cnuGroup_regx') ;
        this.groups = app.store.all('groups')
            .filter(group => [Group.GUEST_ID].indexOf(group.id()) === -1);
        return [
         <div className="Form">
            <div className="Form-group">
                <label>Default group</label>
                <select name="defaultGroup" bidi={this.defaultGroup}>
                    {this.groups.map(group =>
                    <option value={group.id()}>
                        {group.namePlural()}
                    </option>
                    )}
                </select>
            </div>
            
            <div className="Form-group">
                <label>cnu group</label>
                <select name="cnuGroup" bidi={this.cnuGroup}>
                    {this.groups.map(group =>
                    <option value={group.id()}>
                        {group.namePlural()}
                    </option>
                    )}
                </select>
            </div>
            
            <div className="Form-group">
                <label>email regx</label>
                <input className="FormControl" bidi={this.cnuGroupRegx}/>
            </div>
        </div>
        ];
    }
}
