<?php

class AchievmentSeeder extends Seeder
{
    /** @var \Faker\Generator */
    protected $_faker;

    public function __construct(){
        $this->_faker = Faker\Factory::create('ru_RU');
    }

    public function run()
    {
        // Удаляем предыдущие данные
        DB::table('user_groups')->delete();
        DB::table('achievment_groups')->delete();
        DB::table('user_achievments')->delete();
        DB::table('groups')->delete();
        DB::table('achievments')->delete();

        $userImageDirectory = 'public/img/user/';
        $achievmentImageDirectory = 'public/img/achievment/';

        $this->_cleanImageDirectory($userImageDirectory);
        $this->_cleanImageDirectory($achievmentImageDirectory);

        /** @var Group[] $groups */
        $groups = array();

        $groupsData = array(
            array('title' => 'мальчишки', 'code' => 'male'),
            array('title' => 'девчонки', 'code' => 'female'),
            array('title' => 'разработчики', 'code' => 'developer'),
            array('title' => 'дизайнеры', 'code' => 'designer'),
            array('title' => 'менеджеры', 'code' => 'manager'),
            array('title' => 'бородачи', 'code' => 'beard'),
        );

        foreach ($groupsData as $group) {
            $groups[$group['code']] = Group::create(array(
                'title' => $group['title'],
                'description' => '',
                'code' => $group['code'],
            ));
        }

        /** @var Achievment[] $achievments */
        $achievments = array();

        for ($i = 0; $i < 10; $i++) {
            $achievment = Achievment::create(array(
                'depth' => $this->_faker->numberBetween(0, 100),
                'outlook' => $this->_faker->numberBetween(0, 100),
                'interaction' => $this->_faker->numberBetween(0, 100),

                'title' => $this->_faker->sentence(3),
                'description' => $this->_faker->paragraph(),
                'image' => $this->_faker->image($achievmentImageDirectory, 100, 100, 'abstract', false),
            ));
            $this->_attachRandomGroups($achievment, $groups);
            $achievments[] = $achievment;
        }

        // Добавляем администратора

        /** @var User $user */
        $user = User::create(array(
            'name' => 'Dmitry Groza',
            'email' => 'boxfrommars@gmail.com',
            'password' => Hash::make('123123'),
            'image' => $this->_faker->image($userImageDirectory, 100, 100, 'people', false),
        ));

        $user->groups()->attach($groups['developer']->id);
        $user->groups()->attach($groups['male']->id);

        $achievmentIds = $this->_getRandomAchievmentIds($achievments);

        // чтобы добавить в таблицу связи данные (is_approved) нужно устроить такую конструкцию (idAch => array('is_approved' => true))
        $user->achievments()->sync(
            array_combine($achievmentIds, array_fill(0, count($achievmentIds), array('is_approved' => true)))
        );

        // Добавляем остальных тестовых пользователей
        for ($i = 0; $i < 16; $i++) {
            /** @var User $user */
            $user = User::create(array(
                'name' => $this->_faker->name,
                'email' => $this->_faker->email,
                'password' => Hash::make('123123'),
                'image' => $this->_faker->image($userImageDirectory, 100, 100, 'people', false),
            ));

            $achievmentIds = $this->_getRandomAchievmentIds($achievments);

            // чтобы добавить в таблицу связи данные (is_approved) нужно устроить такую конструкцию (idAch => array('is_approved' => true))
            $user->achievments()->sync(
                array_combine($achievmentIds, array_fill(0, count($achievmentIds), array('is_approved' => true)))
            );

            $this->_attachRandomGroups($user, $groups);
        }
    }

    /**
     * @param string $directory
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
     * @param Achievment[] $achievments
     * @return array
     */
    protected function _getRandomAchievmentIds($achievments) {
        return array_map(
            function ($item) {
                return $item->id;
            },
            $this->_faker->randomElements($achievments, rand(2, 6))
        );
    }

    /**
     * @param User|Achievment|\Illuminate\Database\Eloquent\Model $entity
     * @param Group[] $groups
     */
    protected function _attachRandomGroups($entity, $groups){
        $index = $this->_faker->randomNumber();

        $entity->groups()->attach(($index % 2 === 0) ? $groups['male']->id : $groups['female']->id);

        switch ($index % 3) {
            case 0:
                $entity->groups()->attach($groups['developer']->id);
                break;
            case 1:
                $entity->groups()->attach($groups['manager']->id);
                break;
            case 2:
                $entity->groups()->attach($groups['designer']->id);
                break;
        }

        if ($index % 6 === 0) {
            $entity->groups()->attach($groups['beard']->id);
        }
    }
}