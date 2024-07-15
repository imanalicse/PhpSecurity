Use binding or parameterise query to prevent `SQL Injection` attack.
Use h() method to display template variable data
Enabled HTTP/2 to prevent `Client-side desync` attack
Set secure flag to create csrfToken cookie to prevent `TLS cookie without secure flag set` attack
Validate csrfToken value from central method such as beforeFilter and throw 400(Bad request) error if invalid
Turn off autocomplete in every password field