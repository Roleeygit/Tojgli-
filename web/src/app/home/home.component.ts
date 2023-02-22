import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.scss']
})
export class HomeComponent implements OnInit {

  facebook = "https://www.facebook.com/kalman13";
  github = "https://github.com/Roleeygit/Tojgli-";
  googlemaps = "https://www.google.com/maps/place/Erzs%C3%A9bet,+F%C5%91+u.+65,+7661/@46.0992651,18.4558574,17z/data=!4m6!3m5!1s0x4742c7d9ae0b8a81:0x16eeeb28bd9078da!8m2!3d46.0992614!4d18.4580461!16s%2Fg%2F11gm_gz68g";

  constructor() { }

  
  ngOnInit(): void {
  }

}
