<script>
export default {
    data() {
        return {
            staffMemberData: {
                firstName: '',
                lastName: '',
                jobTitle: '',
                emailAddress: '',
                phoneNumber: '',
                bio: '',
                profilePicture: null,
                socialMediaLinks: '',
                department: '',
                hireDate: '',
                workLocation: '',
                skills: '',
                education: '',
                awards: '',
                showInSidebar: false
            },
            links: [],
            newLink: {
                type: '',
                url: '',
                label: '',
            },
        };
    },
    methods: {
        onProfilePictureChange(event) {
            const file = event.target.files[0];
            this.staffMemberData.profilePicture = file;
        },
        addLink() {
            this.links.push({
                type: this.newLink.type,
                url: this.newLink.url,
                label: this.newLink.label,
            });
            this.newLink.type = '';
            this.newLink.url = '';
            this.newLink.label = '';
        },
    }
};
</script>

<template>
    <div class="staff-member-form">
        <form @submit.prevent="handleSubmit">
            <div>
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" v-model="staffMemberData.firstName" />
            </div>
            <div>
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" v-model="staffMemberData.lastName" />
            </div>
            <div>
                <label for="job_title">Job Title:</label>
                <input type="text" id="job_title" v-model="staffMemberData.jobTitle" />
            </div>
            <div>
                <label for="email">Email Address:</label>
                <input type="email" id="email" v-model="staffMemberData.email" />
            </div>
            <div>
                <label for="phone_number">Phone Number:</label>
                <input type="text" id="phone_number" v-model="staffMemberData.phoneNumber" />
            </div>
            <div>
                <label for="bio">Bio/Description:</label>
                <textarea id="bio" v-model="staffMemberData.bio"></textarea>
            </div>
            <div class="form-group">
                <label for="profile-picture">Profile Picture:</label>
                <input type="file" id="profile-picture" v-on:change="onProfilePictureChange">
            </div>
            <ul>
                <li v-for="(link, index) in links" :key="index">
                    <a :href="link.url">{{ link.label || link.type }}</a>
                    <div class="social-links">
                        <div>
                            <label for="social-media-type">Social Media Type:</label>
                            <select id="social-media-type" v-model="newLink.type">
                                <option value="">Select</option>
                                <option value="Facebook">Facebook</option>
                                <option value="Twitter">Twitter</option>
                                <option value="Instagram">Instagram</option>
                                <option value="LinkedIn">LinkedIn</option>
                                <option value="YouTube">YouTube</option>
                            </select>
                        </div>
                        <div>
                            <label for="social-link">Link</label>
                            <input type="text" id="social-link" v-model="newLink.url">
                        </div>
                        <button @click="addLink">-</button>
                    </div>
                </li>
            </ul>
            <div class="social-links">
                <div>
                    <label for="social-media-type">Social Media Type:</label>
                    <select id="social-media-type" v-model="newLink.type">
                        <option value="">Select</option>
                        <option value="Facebook">Facebook</option>
                        <option value="Twitter">Twitter</option>
                        <option value="Instagram">Instagram</option>
                        <option value="LinkedIn">LinkedIn</option>
                        <option value="YouTube">YouTube</option>
                    </select>
                </div>
                <div>
                    <label for="social-link">Link</label>
                    <input type="text" id="social-link" v-model="newLink.url">
                </div>
                <button @click="addLink">+</button>
            </div>
            <div>
                <label for="department">Department/Division:</label>
                <input type="text" id="department" v-model="staffMemberData.department" />
            </div>
            <div>
                <label for="hire_date">Hire Date:</label>
                <input type="date" id="hire_date" v-model="staffMemberData.hireDate" />
            </div>
            <div>
                <label for="work_location">Work Location:</label>
                <input type="text" id="work_location" v-model="staffMemberData.workLocation" />
            </div>
            <div>
                <label for="skills">Skills/Expertise:</label>
                <textarea id="skills" v-model="staffMemberData.skills"></textarea>
            </div>
            <div>
                <label for="education">Education:</label>
                <textarea id="education" v-model="staffMemberData.education"></textarea>
            </div>
            <div>
                <label for="awards">Awards/Recognition:</label>
                <textarea id="awards" v-model="staffMemberData.awards"></textarea>
            </div>
            <div>
                <label for="show_in_sidebar">Show in Sidebar:</label>
                <input type="checkbox" id="show_in_sidebar" v-model="staffMemberData.showInSidebar" />
            </div>
            <button type="submit">Save</button>
        </form>
    </div>
</template>

<style scoped>
label {
    display: block;
    width: 100%;
    margin-bottom: 10px;
}

.social-links {
    display: flex;
    justify-content: space-between;
}

input,
textarea,
select {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 16px;
    margin-bottom: 20px;
}

input[type="checkbox"] {
    width: auto;
    margin-right: 10px;
}

textarea {
    height: 150px;
}

button[type="submit"] {
    background-color: #4CAF50;
    border: none;
    color: white;
    padding: 10px 20px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    border-radius: 4px;
    cursor: pointer;
    margin-top: 20px;
}

button[type="submit"]:hover {
    background-color: #3e8e41;
}

.error-message {
    color: red;
    font-size: 14px;
    margin-bottom: 20px;
}
</style>