# 🔐 Authentication System Documentation

## Overview
The authentication system provides secure user registration and login functionality for the Glamour Palace e-commerce platform. It includes comprehensive validation, security features, and a professional user experience.

## Features

### ✅ User Registration
- **Comprehensive Form Validation**: Client-side and server-side validation
- **Password Strength Checker**: Real-time password strength indicator
- **Region-City Mapping**: Dynamic city selection based on Somalia regions
- **Duplicate Prevention**: Username and email uniqueness validation
- **Professional UI**: Modern, responsive design with loading states

### ✅ User Login
- **Flexible Authentication**: Login with username or email
- **Session Management**: Secure session handling
- **Last Login Tracking**: Records user login timestamps
- **Auto-redirect**: Seamless navigation after successful login

### ✅ Security Features
- **Password Hashing**: Bcrypt password encryption
- **Input Sanitization**: Protection against malicious input
- **Session Security**: Secure session management
- **CSRF Protection**: Built-in request validation

## File Structure

```
menfolder/
├── register.php              # Registration form
├── register-handler.php      # Registration backend logic
├── login.php                 # Login form
├── login-handler.php         # Login backend logic
├── logout-handler.php        # Logout functionality
├── test-auth.php            # Authentication system test
└── AUTHENTICATION_README.md  # This documentation
```

## Database Schema

### Users Collection
```javascript
{
  "_id": ObjectId,
  "username": "string (unique)",
  "email": "string (unique)",
  "contact_number": "string",
  "gender": "male|female",
  "region": "string (Somalia region)",
  "city": "string",
  "password": "string (hashed)",
  "status": "active|inactive",
  "role": "user|admin",
  "created_at": "datetime",
  "updated_at": "datetime",
  "last_login": "datetime"
}
```

## API Endpoints

### Registration
- **URL**: `register-handler.php`
- **Method**: `POST`
- **Content-Type**: `application/json`

**Request Body:**
```json
{
  "username": "string",
  "email": "string",
  "contact_number": "string",
  "gender": "male|female",
  "region": "string",
  "city": "string",
  "password": "string",
  "confirm_password": "string"
}
```

**Response:**
```json
{
  "success": true|false,
  "message": "string",
  "user": "object (on success)",
  "redirect": "string (on success)"
}
```

### Login
- **URL**: `login-handler.php`
- **Method**: `POST`
- **Content-Type**: `application/json`

**Request Body:**
```json
{
  "username": "string",
  "password": "string"
}
```

**Response:**
```json
{
  "success": true|false,
  "message": "string",
  "user": "object (on success)",
  "redirect": "string (on success)"
}
```

## Validation Rules

### Registration Validation
- **Username**: Minimum 3 characters, unique
- **Email**: Valid email format, unique
- **Contact Number**: 8-15 characters, alphanumeric + symbols
- **Password**: Minimum 6 characters
- **Password Confirmation**: Must match password
- **Gender**: Must be 'male' or 'female'
- **Region**: Must be valid Somalia region
- **City**: Must be selected after region

### Login Validation
- **Username/Email**: Required, non-empty
- **Password**: Required, non-empty

## Error Handling

### Common Error Messages
- `Field 'field_name' is required`
- `Invalid email format`
- `Passwords do not match`
- `Password must be at least 6 characters long`
- `Email already registered`
- `Username already taken`
- `Invalid username or email`
- `Invalid password`
- `Account is not active`

### HTTP Status Codes
- `200`: Success
- `400`: Bad Request (validation errors)
- `405`: Method Not Allowed
- `500`: Internal Server Error

## Usage Examples

### Testing the System
1. Navigate to `test-auth.php` to verify system functionality
2. Check database connection and collection access
3. Verify user count and recent registrations

### Registration Flow
1. User fills out registration form
2. Client-side validation runs
3. Form submits to `register-handler.php`
4. Server validates input and checks duplicates
5. User is created in database
6. Success message shown with redirect to login

### Login Flow
1. User enters credentials
2. Form submits to `login-handler.php`
3. Server authenticates user
4. Session is created
5. User is redirected to main page

## Security Considerations

### Password Security
- Passwords are hashed using PHP's `password_hash()` function
- Bcrypt algorithm with default cost factor
- Minimum 6 character requirement

### Session Security
- Sessions are started only after successful authentication
- User data is stored in session variables
- Login timestamp is recorded for audit purposes

### Input Validation
- All input is sanitized and validated
- SQL injection protection through MongoDB driver
- XSS protection through proper output encoding

## Troubleshooting

### Common Issues
1. **Registration Error Despite Success**: Check JavaScript console for errors
2. **Login Not Working**: Verify MongoDB connection and user collection
3. **Session Issues**: Check PHP session configuration
4. **Validation Errors**: Ensure all required fields are filled

### Debug Steps
1. Check browser console for JavaScript errors
2. Verify MongoDB connection in `test-auth.php`
3. Check PHP error logs for server-side issues
4. Validate form data in browser developer tools

## Dependencies

### Required PHP Extensions
- `mongodb` extension
- `json` extension
- `session` extension

### Required Files
- `config/mongodb.php` - Database configuration
- `models/User.php` - User model class
- `vendor/autoload.php` - Composer autoloader

## Future Enhancements

### Planned Features
- Email verification system
- Password reset functionality
- Two-factor authentication
- Social media login integration
- Account lockout after failed attempts
- Password expiration policies

### Performance Improvements
- Redis session storage
- Database connection pooling
- Caching layer for user data
- Async validation for better UX

## Support

For technical support or questions about the authentication system:
1. Check this documentation first
2. Run `test-auth.php` to diagnose issues
3. Review PHP error logs
4. Check MongoDB connection status

---

**Last Updated**: December 2024
**Version**: 1.0.0
**Author**: Glamour Palace Development Team
