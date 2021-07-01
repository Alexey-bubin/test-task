## Install :

### 1. Clone
#### https: 
`https://github.com/Alexey-bubin/test-task.git`
#### SSH:
`git@github.com:Alexey-bubin/test-task.git`

### 2. Install

`composer install`

### 3. Set-up DB :
`php bin/console doctrine:migrations:migrate`

`php bin/console doctrine:fixtures:load`

### Docs : 

http://localhost:8000/api/doc