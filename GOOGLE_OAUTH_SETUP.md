# Google OAuth Setup Guide

This application now supports Google OAuth authentication. Here's how to set it up and test it.

## Prerequisites

The Google OAuth integration is already implemented with:
- Laravel Socialite package installed
- Database migration for `google_id` field
- Complete authentication flow in `AuthController`
- UI integration in login page

## Google OAuth Console Setup

1. **Create a Google Cloud Project** (if you don't have one):
   - Go to [Google Cloud Console](https://console.cloud.google.com/)
   - Create a new project or select an existing one

2. **Enable Google+ API**:
   - Navigate to "APIs & Services" > "Library"
   - Search for "Google+ API" and enable it

3. **Create OAuth 2.0 Credentials**:
   - Go to "APIs & Services" > "Credentials"
   - Click "Create Credentials" > "OAuth client ID"
   - Choose "Web application"
   - Add authorized redirect URIs:
     - For development: `http://localhost:8000/auth/google/callback`
     - For production: `https://yourdomain.com/auth/google/callback`

4. **Get Your Credentials**:
   - Copy the Client ID and Client Secret

## Environment Configuration

1. **Update your `.env` file** with the Google OAuth credentials:

```env
GOOGLE_CLIENT_ID=your_google_client_id_here
GOOGLE_CLIENT_SECRET=your_google_client_secret_here
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

2. **Make sure your APP_URL is set correctly**:
```env
APP_URL=http://localhost:8000  # for development
```

## Database Setup

Run the migrations to ensure the `google_id` field exists:

```bash
php artisan migrate
```

## Testing the Google OAuth Flow

1. **Start the Laravel development server**:
```bash
php artisan serve
```

2. **Visit the login page**:
   - Navigate to `http://localhost:8000/login`
   - You should see the "Continue with Google" button

3. **Test the OAuth flow**:
   - Click the Google login button
   - You'll be redirected to Google's authentication page
   - After successful authentication, you'll be redirected back
   - If it's a new user, you'll be asked to enter your phone number
   - After entering the phone number, you'll be logged in

## Routes

The following routes are available for Google OAuth:

- `GET /auth/google/redirect` - Redirects to Google OAuth
- `GET /auth/google/callback` - Handles Google OAuth callback
- `GET /register/phone` - Phone number collection for new Google users
- `POST /register/phone` - Stores phone number and completes registration

## Troubleshooting

### Common Issues:

1. **"redirect_uri_mismatch" error**:
   - Ensure the redirect URI in Google Console matches exactly with your `.env` configuration

2. **"Client ID not found" error**:
   - Check that your `GOOGLE_CLIENT_ID` in `.env` is correct

3. **Database errors**:
   - Run `php artisan migrate` to ensure the `google_id` column exists

4. **Session errors during phone number collection**:
   - This is normal - Google user data is stored in session temporarily during the registration flow

## User Flow

1. **Existing Google Users**: Direct login to dashboard
2. **New Google Users**: 
   - OAuth authentication with Google
   - Phone number collection page
   - Account creation and automatic login

## Security Notes

- Google OAuth credentials should never be committed to version control
- Use strong, unique credentials for production
- The `google_id` field is used to link Google accounts with local user accounts
- Phone number is required for all users as part of the business logic