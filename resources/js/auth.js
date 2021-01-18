export default class Auth {
    constructor(user) {
        this.user = user;
    }

    roles() {
        return this.user?.controle_acessos.map(controle => controle.chave);
    }

    isAdmin() {
        return this.roles().includes('administrativo');
    }

    can($permission) {
        if(this.roles()) {
            return this.roles().includes($permission) || this.isAdmin();
        }

        return false;
    }

    canAny($dataPermissions) {
        let exists = false;
        $dataPermissions.forEach(permission => {
            if (this.roles().includes(permission) || this.isAdmin()) {
                exists = true;
            }
        });

        return exists;
    }
}
