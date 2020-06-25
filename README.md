# ExtraDataBundle

- [Installation](#installation)
- [Traits](#traits)
- [Validator](#validators)

## Installation

**composer.json**:

```javascript
"repositories": [
    {
        "type": "vcs",
        "url":  "ssh://git@200.50.168.30:222/VendorSoftwareFlowdat3/ExtraDataBundle.git"
    }
],
"require": {
    "ik/extra-data-bundle": "dev-master"
},
```

**app/AppKernel.php**:

```php
public function registerBundles()
{
    $bundles = [
        new ExtraBundle\ExtraBundle(),
    ];
    .
    .
}
```

**app/config/config.yml**:

```yml
imports:
    - { resource: "@ExtraBundle/Resources/config/services.yml" }
```

## Traits

- **Entity\Traits\ExtraDataTrait**: Agrega un campo extraData de tipo JSON. Para agregar en una entidad, por ej. ONU

```php
use ExtraDataBundle\Entity\Traits\ExtraDataTrait;

/**
 * @ORM\Entity
 */
class ONU
{

    use ExtraDataTrait;

```

Luego ejecutar 

```bash
$ bin/console doctrine:schema:update --force
```

- **Entity\Traits\ExtraDataWithParentTrait**: Idem a ExtraDataTrait, el campo extraData se calcula como la diferencia del padre y lo que se ingresa.


## Validators


- **ExtraDataBundle\Validator\JSONValidator**: Assert Callback que valida que un campo tenga formato JSON válido. Para agregarlo:

```php

use Symfony\Component\Validator\Constraints as Assert;

...

    /**
     * @var string $extraData
     *
     * @ORM\Column(type="text", nullable=true)
     * 
     * @Assert\Callback(
     *  callback={"ExtraDataBundle\Validator\JSONValidator", "validate"}, 
     *  payload={"field"="extraData"}
     * )
     */
    private $extraData;

```
La option **payload={"field"="extraData"}**, es requerida, es el nombre del campo en el cual se quiere agregar el mensaje de error de validación.

