<?php
namespace app\modules\organization\models\orm;

use app\common\logic\orm\AddressTrait;
use app\common\logic\orm\EmailTrait;
use app\common\db\ActiveRecord;
use app\common\helpers\CommonHelper;
use app\common\logic\orm\HumanTrait;
use app\common\logic\orm\PhoneTrait;
use app\common\logic\orm\Phone;
use app\common\validators\ForeignKeyValidator;
use app\modules\crm\models\orm\EmployeeToSpeciality;
use app\modules\medical\models\orm\Attendance;
use app\modules\medical\models\orm\Speciality;
use app\modules\organization\OrganizationModule;
use app\modules\security\models\orm\User;

/**
 * Class Employee
 *
 * @property string $user_id
 * @property string $first_name
 * @property string $middle_name
 * @property string $last_name
 * @property int $status
 * @property int $sex
 * @property int $education
 * @property string $birthday
 * @property string $skype_bot_id
 * @property string $skype_code
 * @property string $skype_service_url
 * @property-read int $sexName
 * @property-read string $fullName
 * @property-read Phone[] $phones
 *
 * @package Module\Organization
 * @copyright 2012-2019 Medkey
 */
class Employee extends ActiveRecord
{
    const SEX_MALE = 1;
    const SEX_FEMALE = 2;

    use HumanTrait;
    use PhoneTrait;
    use EmailTrait;
    use AddressTrait;

    public static function modelIdentity()
    {
        return ['first_name', 'last_name', 'birthday', 'sex'];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getPosition()
    {
        return $this->hasOne(Position::class, ['id' => 'position_id']);
    }

    public function getEmployeeToSpeciality()
    {
        return $this->hasMany(EmployeeToSpeciality::class, ['employee_id' => 'id']);
    }

//    public function getSpeciality()
//    {
//        return $this->hasOne(Speciality::class, ['id' => 'speciality_id'])
//            ->via('employeeToSpeciality');
//    }

    public function getSpecialities()
    {
        return $this->hasMany(Speciality::class, ['id' => 'speciality_id'])
            ->via('employeeToSpeciality');
    }

    public function getAttendances()
    {
        return $this->hasMany(Attendance::class, ['employee_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [ ['first_name', 'last_name', 'sex', 'birthday'],
                'required',
            ],
            [ ['first_name', 'middle_name', 'last_name'], 'match', 'pattern' => '/^[АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЬЫЪЭЮЯабвгдеёжзийклмнопрстуфхцчшщьыъэюяa-zA-Z\-]+$/' ],

            [ ['first_name', 'last_name', 'middle_name', 'skype_code', 'skype_bot_id', 'skype_service_url' ],
                'string',
            ],
            [ ['skype_code'], 'unique' ],
            [ ['user_id', 'position_id'], ForeignKeyValidator::class, ],
            [ ['status', 'sex', 'education'],
                'integer',
            ],
//            [ 'speciality_id', ForeignKeyValidator::class ],
            [ ['birthday'],
                'filter',
                'filter' => function () {
                    return $this->birthday = \Yii::$app->formatter->asDate($this->birthday, CommonHelper::FORMAT_DATE_DB);
                },
                'skipOnEmpty' => true,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabelsOverride()
    {
        return [
            'fullName' => OrganizationModule::t('common', 'Full name'),
            'user_id' => OrganizationModule::t('common', 'User'),
            'last_name' => OrganizationModule::t('common', 'Last name'),
            'first_name' => OrganizationModule::t('common', 'First name'),
            'middle_name' => OrganizationModule::t('common', 'Middle name'),
            'birthday' => OrganizationModule::t('common', 'Birthday'),
            'sex' => OrganizationModule::t('common', 'Sex'),
            'phones' => OrganizationModule::t('common', 'Phones'),
            'phone.phone' => OrganizationModule::t('common', 'Phone(s)'),
            'emails' => OrganizationModule::t('common', 'E-mail'),
//            'speciality_id' => OrganizationModule::t('employee', 'Speciality'),
        ];
    }
}