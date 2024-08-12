# StarWorth

Hello and welcome to StarWorth!   
	   
StarWorth is a light and simple software on premise to log calendar and events.  
	   
StarWorth is released under GPLv3 license, it is supplied AS-IS and we do not take any responsibility for its misusage.   
	   
StarWorth name comes from a prank on "Star War" meaning our intention to make stars/projects "worth".    
     
First step, use the left side panel password and salt fields to create the hash to insert in the config file. Remember to manually set there also the salt value.   
	   
As you are going to run StarWorth in the PHP process context, using a limited web server or phpfpm user, you must follow some simple directives for an optimal first setup:   
- Check the permissions of your "data" folder in your web app private path; and set its path in the config file.  
- In the data path create a ".SW_history" and ".SW_captchahistory" files and give them the write permission.   
- Finish to setup the configuration file apporpriately, in the specific:  
     <ul>
       <li>Configure the APP_USE appropriately.</li>
       <li>Configure the DISPLAY attributes as required.</li>
       <li>Configure the max history and MAX_AGE items as required (default: 1000 and 3600).</li>	      
     </ul>
     	   
For feedback: posta@elettronica.lol
