# opptunity
# Symfony OAuth2 Authentication 

This is a Symfony 6.4 application with OAuth2 authentication (Google and Facebook)
using KnpUOAuth2ClientBundle

## Prerequisites

Make sure you have the following installed on your system:

- PHP (>= 8.1)
- Composer
- [Node.js](https://nodejs.org/en/download/)
- [Symfony CLI](https://www.example.com)

## Setup Instructions

1. **Clone the Repository:**

   ```bash
   git clone https://github.com/HETR/opptunity.git
   cd opptunity
   ```

2. **Install Dependencies:**
   
  ```bash
  composer install
  npm install
  ```

3. **Database Setup:**

   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```
   
5. **OAuth2 Configuration:**
   - Obtain OAuth2 credentials from the Google and Facebook Developer Consoles.
   - Update GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, FACEBOOK_CLIENT_ID, and FACEBOOK_CLIENT_SECRET in your .env file.

6. **Build Assets:**
   ```bash
   npm run build
   ```

7. **Run the Symfony Development Server:**
   ```bash
   symfony server:start
   ```

8. **Access the /login Route:**
   Navigate to http://localhost:8000/login to initiate the OAuth2 authentication process. You should see options to log in with Google and Facebook.
   
