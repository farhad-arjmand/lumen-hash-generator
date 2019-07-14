# 3. Usage

## Table of contents

  1. [Installation and Setup](1-Installation-and-Setup.md)
  2. [Configuration](2-Configuration.md)
  3. [Usage](3-Usage.md)

### API: Register

    POST /hash/auth/regiter

###### Body

    {
        'name'    : 'John Dou',
        'email'   : 'example@gmail.com',
        'country' : 'USA',
        'password': '123456'
    }
    
###### Respond
    
    {
        'token': '...'
    }
    
### API: Login    
        
    POST /hash/auth/login
    
###### Body

    {
        'email'   : 'Your Email',
        'password': 'Your Password',
    }
    
###### Respond
    
    {
        'token': '...'
    }
    
### API: Generate Hash 
        
    POST /hash/generator
    
###### Body
   
    {
        'token': 'your token',
    }
    
###### Respond

    {
        'hash': '...'
    }     