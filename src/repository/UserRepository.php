findByEmail(string $email): ?userModel   // ou bool, dependendo de como você decidir
findByUsername(string $username): ?userModel
findByCellphone(string $cellphone): ?userModel
create(userModel $user): void


