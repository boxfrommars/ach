<?php

class AchievmentSeeder extends Seeder
{
    /** @var \Faker\Generator */
    protected $_faker;

    public function __construct()
    {
        $this->_faker = Faker\Factory::create('ru_RU');
    }

    public function run()
    {
        $usersCount = 16;
        $achievmentsCount = 10;
        $beardFrequency = 4; // на сколько мальчишек один бородач
        $defaultPassword = '123123';

        // а вот и все группы
        $groupsData = array(
            array('title' => 'мальчишки', 'code' => 'male'),
            array('title' => 'девчонки', 'code' => 'female'),
            array('title' => 'разработчики', 'code' => 'developer'),
            array('title' => 'дизайнеры', 'code' => 'designer'),
            array('title' => 'менеджеры', 'code' => 'manager'),
            array('title' => 'бородачи', 'code' => 'beard'),
        );

        $userImageDirectory = 'public/img/user/';
        $achievmentImageDirectory = 'public/img/achievment/';

        // Удаляем предыдущие данные
        DB::table('user_groups')->delete();
        DB::table('achievment_groups')->delete();
        DB::table('user_achievments')->delete();
        DB::table('groups')->delete();
        DB::table('achievments')->delete();
        DB::table('users')->delete();

        $this->_cleanImageDirectory($userImageDirectory);
        $this->_cleanImageDirectory($achievmentImageDirectory);

        /** @var Group[] $groups */
        $groups = array();

        foreach ($groupsData as $group) {
            $groups[$group['code']] = Group::create(array(
                'title' => $group['title'],
                'description' => '',
                'code' => $group['code'],
            ));
        }

        /** @var Achievment[] $achievments */
        $achievments = array();

        for ($i = 0; $i < $achievmentsCount; $i++) {
            $achievment = Achievment::create(array(
                'depth' => $this->_faker->numberBetween(0, 100),
                'outlook' => $this->_faker->numberBetween(0, 100),
                'interaction' => $this->_faker->numberBetween(0, 100),

                'title' => $this->_faker->sentence(3),
                'description' => $this->_faker->paragraph(),
                'image' => $this->_faker->image($achievmentImageDirectory, 100, 100, 'abstract', false),
            ));

            $achievment->groups()->sync($this->_getRandomIds($groups));
            $achievments[] = $achievment;
        }

        // Добавляем администратора
        /** @var User $user */
        $userData = array(
            'name' => 'Dmitry Groza',
            'email' => 'boxfrommars@gmail.com',
            'password' => Hash::make($defaultPassword),
            'image' => $this->_faker->image($userImageDirectory, 100, 100, 'people', false),
        );
        $userGroupIds = array($groups['developer']->id, $groups['male']->id);
        $userAchievmentIds = $this->_getRandomIds($achievments, 4);

        $this->_createUser($userData, $userGroupIds, $userAchievmentIds);

        // Добавляем остальных тестовых пользователей
        for ($i = 0; $i < $usersCount; $i++) {

            $gender = $this->_faker->randomElement(array('male', 'female'));

            /** @var User $user */
            $userData = array(
                'name' => mb_convert_case($this->_faker->name($gender), MB_CASE_TITLE), // у фейкера нехорошие имена/фамилии, то с большой буквы, то с маленькой. приводим к нормальному виду
                'email' => $this->_faker->email,
                'password' => Hash::make($defaultPassword),
                'image' => $this->_faker->image($userImageDirectory, 100, 100, 'people', false),
            );

            $position = $this->_faker->randomElement(array('developer', 'manager', 'designer'));
            $userGroupIds = array($groups[$gender]->id, $groups[$position]->id);
            $userAchievmentIds = $this->_getRandomIds($achievments, 4);

            // добавляем немного бородачей
            if ($gender === 'male' && rand(1, $beardFrequency) === 1) {
                array_push($userGroupIds, $groups['beard']->id);
            }

            $this->_createUser($userData, $userGroupIds, $userAchievmentIds);
        }
    }

    /**
     * @param array $data данные, которые прямиком отправляются в User::create($data)
     * @param array $groupIds массив id групп
     * @param array $achievmentIds массив id достижений
     */
    protected function _createUser($data, $groupIds, $achievmentIds)
    {
        $user = User::create($data);
        $user->groups()->sync($groupIds);

        if (!empty($achievmentIds)) { // тут, в отличии от groups нужна проверка, т.к. array_fill вторым параметром  принимает только integer > 0
            $user->achievments()->sync(
                array_combine($achievmentIds, array_fill(0, count($achievmentIds), array('is_approved' => true)))
            );
        }
    }

    /**
     * @param string $directory директория для очищения
     *
     * очищаем директорию, при этом не удалянм в ней файл .gitignore
     */
    protected function _cleanImageDirectory($directory)
    {
        if (File::isDirectory($directory)) {
            $items = new FilesystemIterator($directory);
            foreach ($items as $item) {
                if (!$item->isDir() && $item->getFilename() !== '.gitignore') {
                    File::delete($item->getRealPath());
                }
            }
        }
    }

    /**
     * @param Eloquent[] $entities массив объектов со свойством id
     * @param integer    $maxCount максимальное число возвращаемых id
     * @return array массив id случайно выбранных объектов из списка
     */
    protected function _getRandomIds($entities, $maxCount = null)
    {
        if ($maxCount === null) {
            $maxCount = count($entities);
        }

        return array_map(
            function ($item) {
                return $item->id;
            },
            $this->_faker->randomElements($entities, rand(1, $maxCount))
        );
    }
}