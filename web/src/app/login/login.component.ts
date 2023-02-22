import { Component, OnInit } from '@angular/core';
import { FormBuilder, FormGroup } from '@angular/forms';
import { AuthService } from '../shared/auth.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export class LoginComponent implements OnInit {

  loginForm !: FormGroup

  
  constructor
  (
    private formBuilder: FormBuilder,
    private auth: AuthService
  ) { }

  ngOnInit(): void 
  {
    this.loginForm = this.formBuilder.group({
      email: [''],
      username: [''],
      password: ['']
    });
  }

  login() {
    let email = this.loginForm.value.email;
    let username = this.loginForm.value.username;
    let password = this.loginForm.value.password;

    this.auth.login(email, username, password)
    .subscribe({
      next: data => {
        console.log(data.token)
        console.log(data.name)
        localStorage.setItem('token', data.token);
        localStorage.setItem('name', data.name);


      },
      error: err => {
        console.log('Hiba! Az azonosítás sikertelen!')
      }
    });
  }

}
