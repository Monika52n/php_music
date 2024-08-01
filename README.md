# Music Playlist Management Application

The Music Playlist Management Application allows users to create, manage, and explore music playlists. Users can register and log in to access personalized features, including creating their own playlists and adding or removing songs. Admin users have additional capabilities to manage the music database. The application also features a robust search functionality for discovering music.

### Required Features

1. **Home Page**
   - Displays the names of all public playlists, allowing users to browse through available collections.
   - Shows the number of songs in each playlist along with the name of the user who created it, providing context and attribution.
   - Allows users to click on a playlist to view its detailed contents.
   - Features a search box that filters and displays songs based on the entered search text, making it easy to find specific tracks.

2. **Details Page**
   - Provides detailed information about the songs in a selected playlist, including title, artist, and duration.
   - Displays the total playtime of the playlist, summarizing the length of all included tracks.
   - 
### Basic Features

1. **Login**
   - Handles error cases gracefully, providing feedback for incorrect login attempts.
   - Supports successful login, enabling users to access personalized features.

2. **Registration Form**
   - Includes fields for name, email, password, and password confirmation to ensure comprehensive user information.
   - Manages errors with clear messages and state management to guide users through the registration process.
   - Confirms successful registration, allowing new users to join the platform.

3. **User Playlists**
   - Enables logged-in users to create their own playlists, fostering personalized music collections.
   - Allows users to add songs to their playlists, enhancing their music experience.
   - Provides options to remove songs from their playlists, giving users control over their collections.
   - Supports both public and hidden playlists, with accurate reflections in the listing based on privacy settings.

4. **Admin Functions**
   - Facilitates admin login for accessing advanced features.
   - Grants admin users the ability to create new songs, expanding the platform's music library.
   - Allows admins to modify song details, ensuring the accuracy and relevance of the content.
   - Permits admins to delete songs, maintaining the quality and integrity of the music collection.
