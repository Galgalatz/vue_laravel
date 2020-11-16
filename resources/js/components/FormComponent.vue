<template>
  <div class="container text-right mt-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card">
           <div class="alert alert-danger" v-if="error">
            {{ error }}
          </div>
              <div class="alert alert-success" v-if="success">
              <p>תודה רבה, פרטיך נקלטו במערכת!</p>
            </div>
          <div class="card-header text-center bg-info">
            <h1 class="text-light">פרטים למילוי</h1>
          </div>
          <div class="card-body">
            <form @submit.prevent="submit" autocomplete="off" novalidate="novalidation">
              <label for="name" class="mt-2">שם מלא</label>
              <input type="text" class="form-control" name="name" v-model="fields.name" />
              <small v-show="errName" class="text-danger d-block">{{errName}}</small>

              <label for="phone" class="mt-2">טלפון</label>
              <input type="tel" class="form-control" name="phone" v-model="fields.phone" />
              <small v-show="errPhone" class="text-danger d-block">{{errPhone}}</small>

              <label for="email" class="mt-2">אימייל</label>
              <input type="email" class="form-control" name="email" v-model="fields.email" />
              <small v-show="errEmail" class="text-danger d-block">{{errEmail}}</small>

              <label for="user_id" class="mt-2">תעודת זהות</label>
              <input type="tel" class="form-control" name="user_id" v-model="fields.user_id" />
              <small v-show="errID" class="text-danger d-block">{{errID}}</small>

              <button class="btn btn-info text-light btn-block mt-4">
                אישור
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

export default {
  data: function () {
    return {
      errors: {},
      error: '',
      success: false,
      handler: false,
      errEmail: '',
      errPhone: '',
      errName: '',
      errID: '',
      nameRegex: /^[a-z \u0590-\u05fe]+$/,
      regEmail: /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,24}))$/,
      regPhone: /^(?:0(?!(5|7))(?:2|3|4|8|9))(?:-?\d){7}$|^(0(?=5|7)(?:-?\d){9})$/,
      fields: {
            name: '',
            email: '',
            phone: '',
            user_id: ''
          },
      emailHandler: false,
      phoneHandler: false,
      nameHandler: false,
      idHandler: false
    }
  },
  methods: {
    submit: function () {
      if(!this.fields.name.trim()){
        this.errName = 'אנא הקלד שם';
      }
      else if( !this.nameRegex.test(this.fields.name.trim()) ){
        this.errName = 'אנא הקלד/י שם תקין';
      }else{
         this.errName = '';
         this.nameHandler = true;
      }

     if(!this.fields.phone.trim()){
        this.errPhone = 'אנא הקלד טלפון';
      }

      else if(!this.regPhone.test(this.fields.phone.trim()) ){
         this.errPhone = 'אנא הקלד/י טלפון תקין';
      }else{
         this.errPhone = '';
         this.phoneHandler = true;
      }

     if(!this.fields.email.trim()){
        this.errEmail = 'אנא הקלד/י כתובת מייל';
      }

     else if(!this.regEmail.test(this.fields.email.trim()) ){
         this.errEmail = 'אנא הקלד/י כתובת מייל תקינה';
      }else{
        this.errEmail = '';
        this.emailHandler = true;
      }

  
    if(!this.fields.user_id.trim()){
      this.errID = 'אנא הקלד/י מספר ת.ז';
    }
    else if(!this.checkID(this.fields.user_id)){
        this.errID = 'אנא הקלד/י מספר ת.ז תקין';    
    }else{
      this.errID = '';
      this.idHandler = true;
    }

    if(this.nameHandler && this.phoneHandler && this.emailHandler && this.idHandler){    
      axios.post("/submit", this.fields)
        .then((response) => {
            if(response.data){
                if(response.data.status == 'success'){
                    // console.log(response.data.hbJson);
                let vm = this;

//##2
              axios.post("/process", response.data.hbJson)
                .then((response) => {
                    if(response.data){
                      let user_ob = {
                          'שם_מלא': vm.fields.name,
                          'טלפון': vm.fields.phone,
                          'אימייל': vm.fields.email,
                          'תעודת_זהות': vm.fields.user_id,
                      }
                      let url = response.data;
                      axios.post(url, user_ob)
                          .then((response) => {   
                            if(response.data.message){
                               vm.success = true; 
                               this.fields = {}; 
                            }                    
                                                                
                          })
                        .catch((error) => {
                            console.log(error);
              });

                    }          
                })
              .catch((error) => {

              });

//end#2
                }else{
                  this.error = response.data.errmsg;
                }
            }
        })
        .catch((error) => {
        if (error.response.status === 422) {
          this.errors = error.response.data.errors || {};
          this.errName = this.errors.name ? this.errors.name[0] : '';
          this.errEmail = this.errors.email ? this.errors.email[0] : '';
          this.errPhone = this.errors.phone ? this.errors.phone[0] : '';
          this.errID = this.errors.user_id ? this.errors.user_id[0] : '';
        }
        });
    }
    
    },

    checkID: function(val){
      let IDnum = val;

		if (IDnum.length > 9) {
			IDnum = IDnum.substring(0, 9);
		}

		// Validate correct input
		if ((IDnum.length > 9) || (IDnum.length < 5)) return false;
		if (isNaN(IDnum)) {
			return false;
		}

		// The number is too short - add leading 0000
		if (IDnum.length < 9) {
			while (IDnum.length < 9) {
				IDnum = '0'+IDnum;
			}
		}

		// CHECK THE ID NUMBER
		let mone = 0, incNum;
		let tempIdNum = parseInt(IDnum);

		if (tempIdNum == 0) {
			return false;
		}

		for (let i = 0; i < 9; i ++) {
			incNum = Number(IDnum.charAt(i));
			incNum *= (i % 2)+1;
			if (incNum > 9) incNum -= 9;
			mone += incNum;
		}

		if (mone % 10 == 0) {
			return true;
		} else {
			return false;
		}

    }
  },
 
}
</script>
