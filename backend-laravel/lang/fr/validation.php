<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Lignes de langage de validation
    |--------------------------------------------------------------------------
    |
    | Les lignes de langage suivantes contiennent les messages d'erreur par défaut
    | utilisés par la classe de validation. Certaines de ces règles ont plusieurs
    | versions telles que les règles de taille. N'hésitez pas à ajuster
    | chaque message ici.
    |
    */

    'accepted'             => 'Le champ :attribute doit être accepté.',
    'accepted_if'          => 'Le champ :attribute doit être accepté lorsque :other vaut :value.',
    'active_url'           => "Le champ :attribute n'est pas une URL valide.",
    'after'                => 'Le champ :attribute doit être une date postérieure au :date.',
    'after_or_equal'       => 'Le champ :attribute doit être une date postérieure ou égale au :date.',
    'alpha'                => 'Le champ :attribute ne peut contenir que des lettres.',
    'alpha_dash'           => 'Le champ :attribute ne peut contenir que des lettres, des chiffres et des tirets.',
    'alpha_num'            => 'Le champ :attribute ne peut contenir que des lettres et des chiffres.',
    'array'                => 'Le champ :attribute doit être un tableau.',
    'ascii'                => 'Le champ :attribute ne doit contenir que des caractères alphanumériques et des symboles ASCII à un octet.',
    'before'               => 'Le champ :attribute doit être une date antérieure au :date.',
    'before_or_equal'      => 'Le champ :attribute doit être une date antérieure ou égale au :date.',
    'between' => [
        'numeric' => 'La valeur de :attribute doit être comprise entre :min et :max.',
        'file'    => 'La taille du fichier :attribute doit être comprise entre :min et :max kilo-octets.',
        'string'  => 'Le texte :attribute doit contenir entre :min et :max caractères.',
        'array'   => 'Le tableau :attribute doit contenir entre :min et :max éléments.',
    ],
    'boolean'              => 'Le champ :attribute doit être vrai ou faux.',
    'confirmed'            => 'La confirmation du champ :attribute ne correspond pas.',
    'current_password'     => 'Le mot de passe est incorrect.',
    'date'                 => 'Le champ :attribute n\'est pas une date valide.',
    'date_equals'          => 'Le champ :attribute doit être une date égale au :date.',
    'date_format'          => 'Le champ :attribute ne correspond pas au format :format.',
    'decimal'              => 'Le champ :attribute doit comporter :decimal décimales.',
    'declined'             => 'Le champ :attribute doit être refusé.',
    'declined_if'          => 'Le champ :attribute doit être refusé lorsque :other vaut :value.',
    'different'            => 'Les champs :attribute et :other doivent être différents.',
    'digits'               => 'Le champ :attribute doit avoir :digits chiffres.',
    'digits_between'       => 'Le champ :attribute doit avoir entre :min et :max chiffres.',
    'dimensions'           => 'Les dimensions de l\'image :attribute ne sont pas valides.',
    'distinct'             => 'Le champ :attribute a une valeur en double.',
    'email'                => 'Le champ :attribute doit être une adresse email valide.',
    'ends_with'            => 'Le champ :attribute doit se terminer par l\'une des valeurs suivantes : :values.',
    'enum'                 => 'La valeur sélectionnée pour :attribute est invalide.',
    'exists'               => 'La valeur sélectionnée pour :attribute est invalide.',
    'file'                 => 'Le champ :attribute doit être un fichier.',
    'filled'               => 'Le champ :attribute doit avoir une valeur.',
    'gt' => [
        'numeric' => 'La valeur de :attribute doit être supérieure à :value.',
        'file'    => 'La taille du fichier :attribute doit être supérieure à :value kilo-octets.',
        'string'  => 'Le texte :attribute doit contenir plus de :value caractères.',
        'array'   => 'Le tableau :attribute doit contenir plus de :value éléments.',
    ],
    'gte' => [
        'numeric' => 'La valeur de :attribute doit être supérieure ou égale à :value.',
        'file'    => 'La taille du fichier :attribute doit être supérieure ou égale à :value kilo-octets.',
        'string'  => 'Le texte :attribute doit contenir au moins :value caractères.',
        'array'   => 'Le tableau :attribute doit contenir au moins :value éléments.',
    ],
    'image'                => 'Le champ :attribute doit être une image.',
    'in'                   => 'La valeur sélectionnée pour :attribute est invalide.',
    'in_array'             => 'Le champ :attribute n\'existe pas dans :other.',
    'integer'              => 'Le champ :attribute doit être un entier.',
    'ip'                   => 'Le champ :attribute doit être une adresse IP valide.',
    'ipv4'                 => 'Le champ :attribute doit être une adresse IPv4 valide.',
    'ipv6'                 => 'Le champ :attribute doit être une adresse IPv6 valide.',
    'json'                 => 'Le champ :attribute doit être une chaîne JSON valide.',
    'lowercase'            => 'Le champ :attribute doit être en minuscules.',
    'lt' => [
        'numeric' => 'La valeur de :attribute doit être inférieure à :value.',
        'file'    => 'La taille du fichier :attribute doit être inférieure à :value kilo-octets.',
        'string'  => 'Le texte :attribute doit contenir moins de :value caractères.',
        'array'   => 'Le tableau :attribute doit contenir moins de :value éléments.',
    ],
    'lte' => [
        'numeric' => 'La valeur de :attribute doit être inférieure ou égale à :value.',
        'file'    => 'La taille du fichier :attribute doit être inférieure ou égale à :value kilo-octets.',
        'string'  => 'Le texte :attribute doit contenir au plus :value caractères.',
        'array'   => 'Le tableau :attribute ne doit pas contenir plus de :value éléments.',
    ],
    'mac_address'          => 'Le champ :attribute doit être une adresse MAC valide.',
    'max' => [
        'numeric' => 'La valeur de :attribute ne peut être supérieure à :max.',
        'file'    => 'La taille du fichier :attribute ne peut dépasser :max kilo-octets.',
        'string'  => 'Le texte :attribute ne peut contenir plus de :max caractères.',
        'array'   => 'Le tableau :attribute ne peut contenir plus de :max éléments.',
    ],
    'max_digits'           => 'Le champ :attribute ne doit pas contenir plus de :max chiffres.',
    'mimes'                => 'Le champ :attribute doit être un fichier de type : :values.',
    'mimetypes'            => 'Le champ :attribute doit être un fichier de type : :values.',
    'min' => [
        'numeric' => 'La valeur de :attribute doit être au moins :min.',
        'file'    => 'La taille du fichier :attribute doit être d\'au moins :min kilo-octets.',
        'string'  => 'Le texte :attribute doit contenir au moins :min caractères.',
        'array'   => 'Le tableau :attribute doit contenir au moins :min éléments.',
    ],
    'min_digits'           => 'Le champ :attribute doit contenir au moins :min chiffres.',
    'missing'              => 'Le champ :attribute doit être absent.',
    'missing_if'           => 'Le champ :attribute doit être absent lorsque :other vaut :value.',
    'missing_unless'       => 'Le champ :attribute doit être absent sauf si :other vaut :value.',
    'missing_with'         => 'Le champ :attribute doit être absent lorsque :values est présent.',
    'missing_with_all'     => 'Le champ :attribute doit être absent lorsque :values sont présents.',
    'not_in'               => 'La valeur sélectionnée pour :attribute est invalide.',
    'not_regex'            => 'Le format du champ :attribute est invalide.',
    'numeric'              => 'Le champ :attribute doit être un nombre.',
    'password' => [
        'letters'       => 'Le champ :attribute doit contenir au moins une lettre.',
        'mixed'         => 'Le champ :attribute doit contenir au moins une majuscule et une minuscule.',
        'numbers'       => 'Le champ :attribute doit contenir au moins un chiffre.',
        'symbols'       => 'Le champ :attribute doit contenir au moins un caractère spécial.',
        'uncompromised' => "Le :attribute donné est apparu dans une fuite de données. Veuillez choisir un autre mot de passe.",
    ],
    'present'              => 'Le champ :attribute doit être présent.',
    'prohibited'           => 'Le champ :attribute est interdit.',
    'prohibited_if'        => 'Le champ :attribute est interdit lorsque :other vaut :value.',
    'prohibited_unless'    => 'Le champ :attribute est interdit sauf si :other est dans :values.',
    'prohibited_with'      => 'Le champ :attribute est interdit lorsque :values est présent.',
    'prohibited_with_all'  => 'Le champ :attribute est interdit lorsque :values sont présents.',
    'regex'                => 'Le format du champ :attribute est invalide.',
    'required'             => 'Le champ :attribute est obligatoire.',
    'required_array_keys'  => 'Le champ :attribute doit contenir des entrées pour :values.',
    'required_if'          => 'Le champ :attribute est obligatoire lorsque :other vaut :value.',
    'required_if_accepted' => 'Le champ :attribute est obligatoire lorsque :other est accepté.',
    'required_unless'      => 'Le champ :attribute est obligatoire sauf si :other est dans :values.',
    'required_with'        => 'Le champ :attribute est obligatoire lorsque :values est présent.',
    'required_with_all'    => 'Le champ :attribute est obligatoire lorsque :values sont présents.',
    'required_without'     => 'Le champ :attribute est obligatoire lorsque :values est absent.',
    'required_without_all' => 'Le champ :attribute est obligatoire lorsque aucun des :values n\'est présent.',
    'same'                 => 'Les champs :attribute et :other doivent correspondre.',
    'size' => [
        'numeric' => 'La valeur de :attribute doit être :size.',
        'file'    => 'La taille du fichier :attribute doit être de :size kilo-octets.',
        'string'  => 'Le texte :attribute doit contenir :size caractères.',
        'array'   => 'Le tableau :attribute doit contenir :size éléments.',
    ],
    'starts_with'          => 'Le champ :attribute doit commencer par l\'une des valeurs suivantes : :values.',
    'string'               => 'Le champ :attribute doit être une chaîne de caractères.',
    'timezone'             => 'Le champ :attribute doit être un fuseau horaire valide.',
    'unique'               => 'La valeur du champ :attribute est déjà utilisée.',
    'uploaded'             => "Le fichier :attribute n'a pu être téléchargé.",
    'uppercase'            => 'Le champ :attribute doit être en majuscules.',
    'url'                  => 'Le format du champ :attribute est invalide.',
    'uuid'                 => 'Le champ :attribute doit être un UUID valide.',

    /*
    |--------------------------------------------------------------------------
    | Lignes de langue pour la validation personnalisée
    |--------------------------------------------------------------------------
    | Placez ici vos propres messages de validation pour attributs avec la
    | convention "attribute.rule". Cela permet de spécifier rapidement
    | un message personnalisé pour un attribut donné.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'message-personnalise',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Attributs personnalisés de validation
    |--------------------------------------------------------------------------
    | Les lignes de langue suivantes sont utilisées pour remplacer les fabriqués
    | :attribute placeholders par quelque chose de plus lisible tel que « Email »
    | au lieu de "email". Cela nous aide à rendre les messages remplacés plus
    | conviviaux.
    |
    */

    'attributes' => [],
];
