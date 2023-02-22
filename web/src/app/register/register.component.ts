import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { AuthService } from '../shared/auth.service';
@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.scss'],
})
export class RegisterComponent implements OnInit {
  
  registerForm !: FormGroup;
  errorMessage!: string;
  
  constructor
  (
    private formBuilder: FormBuilder,
    private auth: AuthService
  ) { }

  ngOnInit(): void 
  {
    this.registerForm = this.formBuilder.group({
      username: ['', Validators.required],
      email: ['', Validators.required],
      password: ['', Validators.required],
      confirm_password: ['', Validators.required]
    });
  }

  register() 
  {
    let username = this.registerForm.value.username
    let email = this.registerForm.value.email
    let password = this.registerForm.value.password
    let confirm_password = this.registerForm.value.confirm_password

    this.auth.register(username, email, password, confirm_password)
    .subscribe
    ({
      next: data => 
      {
        console.log(data.token)
        console.log(data.username)
        console.log(data.email)
        localStorage.setItem('token', data.token);
        localStorage.setItem('username', data.username);
        localStorage.setItem('email', data.email);
      },
      error: err => 
      {
        const errorObj = err.error.data;
        const errorDiv = document.getElementById("error-div");
        if (errorDiv) 
        {
          errorDiv.textContent = '';
          for (const field in errorObj) 
          {
            if (errorObj.hasOwnProperty(field)) 
            {
              const errorMessage = errorObj[field][0];
              errorDiv.textContent += "* " + field + ": " + errorMessage + "\n";
            }
          }
        }
     }});
  }

}