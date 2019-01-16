<template>
    <div class="row justify-content-center">
        
        <!-- start here -->
        <div class="col-6">
            <h3>Add or Edit</h3>
            <form @submit.prevent = "addPerson" class="form-inline mb-2">
                <label class="sr-only" for="firstname">First Name</label>
                <input type="text" class="form-control mb-2 mr-sm-2" id="firstname" placeholder="First Name" v-model="person.firstName" required>

                <label class="sr-only" for="lastname">Last Name</label>
                <input type="text" class="form-control mb-2 mr-sm-2" id="lastname" placeholder="Last Name" v-model="person.lastName" required>
                
                <label class="sr-only" for="birthdate">Birthdate</label>
                <input type="text" class="form-control mb-2 mr-sm-2" id="birthdate" placeholder="Birthdate (YYYY-mm-dd)" v-model="person.birthdate" required>

                <button type="submit" class="btn btn-primary mb-2">Save</button>
            </form>
            <hr>
             <h3>Filter By Age</h3>
            <form mb-2>
                <div class="form-group">
                    <label for="ageRange">Show persons below <span>{{sliderVal}}</span></label>
                    
                    <input v-model="sliderVal" v-bind:style="{ width : maxAgeFilter+'%' }" type="range" min="1" max="100" value="100" class="form-control-range" id="ageRange" @change="filterByAge">
                </div>
            </form>
            <hr>
            <h3>Search by Name</h3>
            <form @submit.prevent = "searchPerson" class="form-inline mb-2">
                <label class="sr-only" for="search_firstname">First Name</label>
                <input type="text" class="form-control mb-2 mr-sm-2" id="s_firstname" placeholder="First Name" v-model="search_firstname">

                <label class="sr-only" for="lastname">Last Name</label>
                <input type="text" class="form-control mb-2 mr-sm-2" id="s_lastname" placeholder="Last Name" v-model="search_lastname">
                
                <button type="submit" class="btn btn-info mb-2">Search</button>
            </form>

            
            
        </div>

        
        <div class="col-6 offset-mb-2">
                <table class="table">
                            <thead>
                                <tr>
                                <th scope="col">#</th>
                                <th scope="col">First</th>
                                <th scope="col">Last</th>
                                <th scope="col">Birthdate</th>
                                <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="person in persons" :key="person.id">
                                <th scope="row">{{person.id}}</th>
                                <td>{{person.firstName}}</td>
                                <td>{{person.lastName}}</td>
                                <td>{{person.birthdate}}</td>
                                <td>
                                        <button @click="editPerson(person)" class="btn btn-info">Edit</button>
                                        <button @click="deletePerson(person.id)" class="btn btn-danger">Delete</button>
                                </td>
                                </tr>
                                </tbody>
                </table>

                <!-- pagination -->
                <nav>
                    <ul class="pagination">
                                <li v-for="page in pagination" :key="page" class="page-item" v-bind:class="{ active: page == current_page }">
                                    <a class="page-link" href="#" @click="changePage(page)">{{page}}</a>
                                </li>
                    </ul>
                </nav>
                <button @click="reloadAll()" type="button" class="btn btn-secondary btn-lg btn-block">Reload All</button>
        </div>
        
        

    </div>
</template>

<script>
    export default{
        data(){
            return {
                persons : [],
                person : {
                    id: '',
                    firstName: '',
                    lastName: '',
                    birthdate: ''
                },
                person_id : '',
                pagination : [],
                edit: false,
                current_page : 1,
                sliderVal : 100,
                maxAgeFilter : 100,
                url : 'api/persons',
                search_firstname : '',
                search_lastname : ''
            }
        },

        created() {
            this.fetchPersons();
        },

        mounted: function() {
            
            var vm = this;

            $(function() {
                $( "#birthdate" ).datepicker({
                    
                    dateFormat : 'yy-mm-dd',
                    maxDate: 0,
                    changeMonth: true,
                    changeYear: true,
                    onClose: function (dateText, inst) {
                        var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                        var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                        $(this).datepicker('setDate', new Date(year, month, 1));  
                    }
                }).on('change', function(e) {
                    var newdate = $( "#birthdate" ).val();
                    vm.person.birthdate = newdate;
                    //console.log(newdate);
                });
            });
        },

        methods: {
            fetchPersons(){
                fetch(this.url)
                .then( res => res.json() )
                .then(res => {
                    
                    this.pagination = [];
                    //check if there is a need for pagination links
                    if(res.meta.to > 1){
                        
                        for(var i = 1; i <= res.meta.last_page; i++){
                            this.pagination.push(i);
                        }
                    }
                    //show results
                    this.persons = res.data;
                })
            },
            changePage(page){
                this.current_page = page;
                if(this.sliderVal < this.maxAgeFilter){
                    this.url = 'api/person/agefilter/'+this.sliderVal+'/?page='+this.current_page;
                }else if(this.search_firstname != '' || this.search_lastname != ''){
                    this.url = 'api/person/search/'+this.search_firstname+'/'+this.search_lastname+'/?page='+this.current_page; 
                }else{
                    this.url = 'api/persons/?page='+this.current_page;
                }

                this.fetchPersons();
            },
            deletePerson(id){
                if(confirm('Are you sure?')){
                    fetch(`api/person/${id}`,{
                        method: 'delete'
                    })
                    .then(res => res.json())
                    .then(data => {
                        alert('Person data deleted.');
                        this.fetchPersons();
                    })
                    .catch(err => console.log(err));
                }
            },
            addPerson(){
                       if(this.edit === false){
                            //add new person
                                fetch(`api/person`,{
                                    method : 'post',
                                    body : JSON.stringify(this.person),
                                    headers :{
                                        'content-type' : 'application/json'
                                    }
                                })
                                .then(res => res.json())
                                .then(data => {
                                    this.person.firstName = '';
                                    this.person.lastName = '';
                                    this.person.birthdate = '';
                                    alert('New Person added');
                                    this.fetchPersons();
                                })
                                .catch(err => console.log(err));
                        }else{
                                    fetch(`api/person`,{
                                    method : 'put',
                                    body : JSON.stringify(this.person),
                                    headers :{
                                        'content-type' : 'application/json'
                                    }
                                    })
                                    .then(res => res.json())
                                    .then(data => {
                                        this.person.firstName = '';
                                        this.person.lastName = '';
                                        this.person.birthdate = '';
                                        this.fetchPersons();
                                        alert('Record Updated');
                                        
                                    })
                                    .catch(err => console.log(err));
                        }
                
                
            },
            editPerson(person){
                this.edit = true;
                this.person.id = person.id;
                this.person.firstName = person.firstName;
                this.person.lastName = person.lastName;
                this.person.birthdate = person.birthdate;
                this.person_id = person.id;
                $( "#birthdate" ).val(this.person.birthdate);
            },
            filterByAge(){
                this.search_firstname = '';
                this.search_lastname = '';
                this.url = 'api/person/agefilter/'+this.sliderVal;
                this.fetchPersons();     
            },
            searchPerson(){
                this.sliderVal = this.maxAgeFilter; //clear age filter
                var fname = this.search_firstname;
                var lname = this.search_lastname;
                if(this.search_firstname == '') {fname=0;}
                else if(this.search_lastname == ''){lname=0;}

                if(fname == 0 && lname == 0){
                        //nothing is searched so show all again
                        this.reloadAll();
                }else{
                        this.url = 'api/person/search/'+fname+'/'+lname;
                          
                }
                this.fetchPersons();
            },
            reloadAll(){
                this.url = 'api/persons';
                this.current_page = 1;
                this.fetchPersons();
            }
        } //end of method
    }        
</script>