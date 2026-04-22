# MSOLM : My Simple OLM

**My Simple OLM** is a PHP classes generator for mapping LDAP directory branches to PHP objects.  
OLM stands for **Object LDAP Mapping**.


> [!WARNING]  
> The generated classes use the MLdap class from mlib (`mlib\net\ldap\MLdap`).


## Usage

### Basic Usage

```bash
# Generate classes for a project
msolm.php -g -p myproject

# Generate classes with custom output directory
msolm.php --generate --project=myproject -o outputdir

# Update existing classes (preserve custom modifications)
msolm.php -g --update -p myproject

# Check project configuration
msolm.php --check -p myproject

# Clean generated files
msolm.php --clean

# Show help
msolm.php -h
```

### Project Configuration

Create a project directory in `projects/` with a `description.xml` file:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<ldap host="ldap.my-corp.org" port="389" 
      bind_dn="cn=manager,dc=my-corp,dc=org" 
      bind_password="" 
      phpNamespace="sample">
    
    <object ou="ou=people,dc=my-corp,dc=org" 
            filter="(personType=staff)" 
            phpObjectName="Staff" 
            phpManagerName="StaffManager">
        
        <attribute name="uid" rdn="true" phpAttributeName="login" />
        <attribute name="empId" phpAttributeName="employeeNumber" />
        <attribute name="sn" phpAttributeName="name" />
        <attribute name="givenName" phpAttributeName="firstname" />
        <attribute name="displayName" />
        <attribute name="mail" />
        <attribute name="affectations" multi="true" phpAttributeName="service" />
        <attribute name="otherMails" multi="true" 
                   phpAttributeName="mailAlias" 
                   phpAttributePFName="mailAliases"/>
    </object>
</ldap>
```

### Configuration Elements

- **ldap**: Main LDAP connection configuration
  - `host`, `port`: LDAP server details
  - `bind_dn`, `bind_password`: Authentication credentials
  - `phpNamespace`: Namespace for generated classes

- **object**: LDAP object mapping
  - `ou`: Organizational unit
  - `filter`: LDAP filter for object selection
  - `phpObjectName`: Name of the generated PHP class
  - `phpManagerName`: Name of the generated manager class

- **attribute**: LDAP attribute mapping
  - `name`: LDAP attribute name
  - `rdn="true"`: Mark as Relative Distinguished Name
  - `phpAttributeName`: PHP property name
  - `multi="true"`: For multi-valued attributes
  - `phpAttributePFName`: Name for plural form methods

## Generated Classes

The tool generates two types of classes:

1. **Object Classes**: Map individual LDAP entries
2. **Manager Classes**: Handle LDAP operations (search, create, update, delete)

## Example

After running the generator with the sample configuration, you'll get classes like:

```php
// Generated Staff class
$staff = new sample\Staff();
$staff->setLogin('jdoe');
$staff->setName('Doe');
$staff->setFirstname('John');

// Generated StaffManager
$manager = sample\StaffManager::getInstance();
$manager->add($staff);
$staffList = $manager->searchByName('Doe');
```

## Documentation

Complete documentation is available at:

- **English**: https://lezab.github.io/myPhp/en/base/msolm/
- **Français**: https://lezab.github.io/myPhp/fr/base/msolm/

