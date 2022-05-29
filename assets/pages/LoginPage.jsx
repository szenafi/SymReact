import React, {useState} from "react";

const LoginPage = (props) => {
    const [credentials, setCredentials] = useState({
        username: "",
        password: "",
    });

const handleChange = ( event ) => {
    const value = event.currentTarget.value;
    const name = event.currentTarget.name;
    setCredentials({ ...credentials, [name]: value });
};

const handleSubmit = ( event ) => {
    event.preventDefault();
    console.log(credentials);
};

  return (
    <>
      <h1>Connexion à l'application</h1>

      <form onSubmit={handleSubmit}>
        <div className="form-group">
          <label htmlFor="username">Adresse Email</label>
          <input
            onChange={handleChange}
            value={credentials.username}
            type="email"
            placeholder="Email"
            name="username"
            id="username"
            className="form-control"
          />
        </div>
        <div className="form-group">
          <label htmlFor="password">Mot de passe</label>
          <input
            onChange={handleChange}
            value={credentials.password}
            type="password"
            placeholder="Mot de passe"
            name="password"
            id="password"
            className="form-control"
          />
        </div>
        <div>
            <button className="btn btn-primary">Connexion</button>
        </div>
      </form>
    </>
  );
};

export default LoginPage;
