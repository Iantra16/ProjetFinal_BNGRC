takalo-takalo/
├── app/
│   ├── controllers/
│   │   ├── AdminController.php
│   │   ├── AuthController.php
│   │   ├── ObjetController.php
│   │   └── EchangeController.php
│   ├── models/
│   │   ├── Database.php
│   │   ├── User.php
│   │   ├── Objet.php
│   │   ├── Categorie.php
│   │   └── Echange.php
│   └── views/
│       ├── admin/
│       │   ├── layout.php
│       │   ├── login.php
│       │   ├── dashboard.php
│       │   ├── categories.php
│       │   ├── categories_form.php
│       │   └── utilisateurs.php
│       └── front/
│           ├── layout.php
│           ├── inscription.php
│           ├── login.php
│           ├── liste_objets.php
│           ├── fiche_objet.php
│           ├── mes_objets.php
│           ├── objet_form.php
│           ├── mes_echanges.php
│           └── historique_objet.php
├── database/
│   └── schema.sql
├── public/
│   ├── assets/
│   │   ├── admin/
│   │   │   └── dist/
│   │   │       ├── css/
│   │   │       │   ├── adminlte.min.css
│   │   │       │   └── adminlte.css
│   │   │       └── js/
│   │   │           ├── adminlte.min.js
│   │   │           └── adminlte.js
│   │   ├── front/
│   │   │   └── dist/
│   │   │       ├── css/
│   │   │       │   ├── adminlte.min.css
│   │   │       │   └── adminlte.css
│   │   │       └── js/
│   │   │           ├── adminlte.min.js
│   │   │           └── adminlte.js
│   │   ├── shared/
│   │   │   ├── css/
│   │   │   │   └── custom.css
│   │   │   └── js/
│   │   │       └── custom.js
│   │   └── uploads/
│   │       └── objets/
│   ├── .htaccess
│   └── index.php
├── vendor/
├── .gitignore
├── composer.json
├── composer.lock
└── README.md