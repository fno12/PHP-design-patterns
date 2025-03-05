<?php
interface UserRoleFlyweight {
    public function getRole(): string;
}

class UserRole implements UserRoleFlyweight {
    private string $role;

    public function __construct(string $role) {
        $this->role = $role;
    }

    public function getRole(): string {
        return $this->role;
    }
}

// Flyweight Factory - manages roles
class UserRoleFactory {
    private static array $roles = [];

    public static function getRole(string $role): UserRoleFlyweight {
        if (!isset(self::$roles[$role])) {
            self::$roles[$role] = new UserRole($role);
        }
        return self::$roles[$role];
    }
}

class User {
    private string $name;
    private string $surname;
    private UserRoleFlyweight $role;

    public function __construct(string $name, string $surname, UserRoleFlyweight $role) {
        $this->name = $name;
        $this->surname = $surname;
        $this->role = $role;
    }

    public function getInfo(): array {
        return [
            "name" => $this->name,
            "surname" => $this->surname,
            "role" => $this->role->getRole()
        ];
    }
}

$users = [];
$users[] = new User("John", "Smith", UserRoleFactory::getRole("Admin"));
$users[] = new User("Michael", "Johnson", UserRoleFactory::getRole("User"));
$users[] = new User("Emily", "Davis", UserRoleFactory::getRole("Admin"));
$users[] = new User("Jessica", "Brown", UserRoleFactory::getRole("User"));
$users[] = new User("Daniel", "Wilson", UserRoleFactory::getRole("Moderator"));

// JSON output
header("Content-Type: application/json");
echo json_encode(array_map(fn($user) => $user->getInfo(), $users), JSON_PRETTY_PRINT);
