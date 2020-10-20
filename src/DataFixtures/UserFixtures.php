<?php


namespace App\DataFixtures;


use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    /** @var UserPasswordEncoderInterface encoderInterface $encoder */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this -> encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        //region role init
        $role_admin = new Role();
        $role_admin -> setLabelRole("ROLE_ADMIN");
        $role_user = new Role();
        $role_user -> setLabelRole("ROLE_USER");
        $manager -> persist($role_admin);
        $manager -> persist($role_user);
        //endregion
        //region user init
        $user_admin = new User();
        $user_admin->setUsername("Admin");
        $user_admin -> setEmail("administrator@admin.com");
        $password_admin = $this -> encoder -> encodePassword($user_admin, 'Admin1234');
        $user_admin -> setPassword($password_admin);
        $user_admin -> addRoleUser($role_admin);

        $user_basic = new User();
        $user_basic->setUsername("Basic");
        $user_basic -> setEmail("user@user.com");
        $password_basic = $this -> encoder -> encodePassword($user_basic, 'Basic1234');
        $user_basic -> setPassword($password_basic);
        $user_basic -> addRoleUser($role_user);

        $manager -> persist($user_admin);
        $manager -> persist($user_basic);
        //endregion

        $manager -> flush();
    }
}