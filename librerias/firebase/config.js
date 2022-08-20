

    function connectionFirebase(){
        const firebaseConfig = {
            apiKey: "AIzaSyBYJ2R7IBPsetEye0ThlLvjmKk_i91FiUI",
            authDomain: "trackmoy-app.firebaseapp.com",
            databaseURL: "https://trackmoy-app-default-rtdb.firebaseio.com",
            projectId: "trackmoy-app",
            storageBucket: "trackmoy-app.appspot.com",
            messagingSenderId: "24425908221",
            appId: "1:24425908221:web:9d45a8dd460d244dc17b94"
          };
          return firebase.initializeApp(firebaseConfig);
    }
