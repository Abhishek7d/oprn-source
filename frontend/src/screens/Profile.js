import React, { Component } from 'react'
import Navigation from '../components/Navigation';
import Sidebar from '../components/Sidebar';
import ApiHandler from '../model/ApiHandler';

class Profile extends React.Component {
    constructor(props) {
        super()
        this.state = {
            newPassword: "",
            oldPassword: "",
            error: "",
            success: "",
        }
        this.apiHandler = new ApiHandler();
    }
    handleChangePassword = () => {
        this.apiHandler.changePassword(this.state.oldPassword, this.state.newPassword, (msg) => {
            this.setState({error: "", success: msg})
            console.log(msg);
        }, (err) => {
            this.setState({error: err, success: ""})
            console.log(err);
        })
    }

    dataChange = (event) => {
        this.setState({
            [event.target.name]: event.target.value
        })
    }

    render() {
        return (
            <>
                <div className="container-fluid p-0">
                    <Navigation />
                    <Sidebar />
                    <div className="content-wrapper">
                        <section className="content-header">
                            <div className="container-fluid">
                                <div className="row mb-2">

                                </div>
                            </div>
                        </section>

                        <section className="content">
                            <div className="container-fluid">
                                <div className="row">
                                    <div className="col-12">
                                        <div className="card card-primary card-outline">
                                            <div className="card-header">
                                                <div className="col-3 float-left">
                                                    Password Change
                                                </div>

                                            </div>
                                            <div className="card-body">
                                                <p style={{ color: "red" }} dangerouslySetInnerHTML={{ __html: this.state.error }}></p>
                                                <p style={{ color: "green" }} dangerouslySetInnerHTML={{ __html: this.state.success }}></p>
                                                <div className="col-4 application_page_cards" id="huddles">
                                                    <div className="form-group">
                                                        <input type="text" className="form-control" value={this.state.oldPassword} name="oldPassword" onChange={this.dataChange} placeholder="old password" />
                                                    </div>
                                                    <div className="form-group">
                                                        <input type="text" className="form-control" value={this.state.newPassword} name="newPassword" onChange={this.dataChange} placeholder="new password" />
                                                    </div>
                                                    <div className="form-group">
                                                        <button className="btn btn-primary" onClick={this.handleChangePassword}>Change Password</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="card-footer"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </>

        )
    }
}
export default Profile;
