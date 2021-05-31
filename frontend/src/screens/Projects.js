import React from 'react'
import Navigation from '../components/Navigation';

import Sidebar from '../components/Sidebar';
import ProjectCard from '../components/ProjectCard';
import ApiHandler from '../model/ApiHandler';
import ProjectDetails from '../components/ProjectDetails';
import { Alert, Modal, Button } from 'react-bootstrap';
import PageHeader from '../components/template/PageHeader';
import { withRouter } from 'react-router';
import Loader from '../components/template/Loader';

class Projects extends React.Component {
    constructor(props) {
        super();
        let projectId = props.match.params.projectId;
        this.state = {
            projects: [],
            selectedProject: null,
            isProjectClicked: false,
            showModal: false,
            loading: true,
            projectName: "",
            screenName: 'Projects',
            error: "",
            success: "",
            projectId: projectId,
            servers: [],
            selectedServers: null,
            accessStatus: null,
        }
        this.apiHandler = new ApiHandler();
    }
    showError = (err) => {

    }
    refreshPage = () => {
        this.getProjects();
        this.getServers();
    }
    componentDidMount() {
        document.title = "Your Projects";
        this.getProjects();
        this.getServers();
    }
    getProjects = () => {
        this.setState({ loading: true, accessStatus: null })
        this.apiHandler.getProjects(0, (msg, data) => {
            data.data.forEach((s) => {
                if (s.uuid === this.state.projectId) {
                    this.setState({
                        selectedProject: s,
                        isProjectClicked: true
                    })
                }
            })
            this.setState({ projects: data.data, loading: false, accessStatus: msg })
        }, err => {
            this.showError(err);
            this.setState({ loading: false, accessStatus: err })

        })
    }
    renderProjects() {
        let projects = [];
        this.state.projects.forEach((data, index) => {
            projects.push(<ProjectCard key={index} data={data} servers={this.state.servers} projectClickHandler={this.projectClickHandler} />);
        })
        return projects;
    }
    projectClickHandler = (project = null) => {
        if (project) {
            this.setState({ selectedProject: project });
            if (!this.state.isProjectClicked) {
                window.history.replaceState(null, null, "/projects/" + project.uuid)
            }
            this.setState({
                isProjectClicked: !this.state.isProjectClicked,
                screenName: project.name
            })
        }
    }
    goBack = () => {
        if (this.state.isProjectClicked) {
            window.history.replaceState(null, null, "/projects")
        }
        this.setState({
            isProjectClicked: !this.state.isProjectClicked,
        })
    }
    handleModalShow = () => {
        this.setState({
            showModal: true,
        })
        this.getServers();
    }
    handleModalClose = () => {
        this.setState({
            showModal: false,
        })
    }
    dataChange = (event) => {
        this.setState({ [event.target.name]: event.target.value })
    }
    handleAddProject = () => {
        let form = document.getElementsByTagName("form")[0]
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        if (this.state.loading) {
            return;
        }
        this.setState({ error: "", success: "", loading: true })
        this.apiHandler.createProject(this.state.projectName, this.state.selectedServers, (message, data) => {
            this.setState({ error: "", success: message, loading: false })
            window.location.href = "/projects"
            console.log(data, message);
        }, (message) => {
            this.setState({ error: message, success: "", loading: false })
            console.log(message);
        });
    }
    getServers = () => {
        this.apiHandler.getServerUnassigned((msg, data) => {
            this.setState({
                servers: data
            })
        }, (data) => {

        });
    }
    onEnterPress = (e) => {
        if (e.keyCode === 13 && e.shiftKey === false) {
            e.preventDefault();
            this.handleAddProject();
        }
    }
    renderServers = () => {
        let select = [];
        this.state.servers.forEach((data, index) => {
            select.push(<option key={index} value={data.id}>{data.name}</option>)
        })
        return select;
    }
    selectChange = (event) => {
        let value = Array.from(event.target.selectedOptions, option => option.value);
        this.setState({ [event.target.name]: value })
    }
    render() {
        return (
            <> <Modal centered show={this.state.showModal} onHide={this.handleModalClose}>
                <form>
                    <Modal.Header closeButton>
                        <Modal.Title>ADD PROJECT</Modal.Title>
                    </Modal.Header>
                    <Modal.Body>
                        <Alert onClose={() => this.setShow()} show={(this.state.error !== "") ? true : false} variant="danger" dismissible>
                            {this.state.error}
                        </Alert>
                        <Alert onClose={() => this.setShow()} show={(this.state.success !== "") ? true : false} variant="success" dismissible>
                            {this.state.success}
                        </Alert>
                        <div className="modal-form">
                            <label htmlFor="projectName">Project Name</label>
                            <div className="input-group">
                                <input required type="text" onKeyDown={this.onEnterPress} className="form-control form-input-field" name="projectName" value={this.state.projectName} onChange={this.dataChange} id="projectName" />
                                <div className="input-group-append">
                                    <div className="input-group-text theme">
                                        <i className="fa fa-desktop"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {
                            (this.state.servers.length > 0) ?
                                <div className="modal-form">
                                    <label htmlFor="assignServers">Assign Servers</label>
                                    <div className="input-group">
                                        <select onChange={this.selectChange} name="selectedServers" className="custom-select" size="1" multiple aria-label="multiple select">
                                            {this.renderServers()}
                                        </select>
                                    </div>
                                </div>
                                :
                                ''
                        }
                    </Modal.Body>
                    <Modal.Footer>
                        <Button variant="default" onClick={this.handleModalClose}>
                            CLOSE
                            </Button>
                        <Button className="btn btn-theme" onClick={this.handleAddProject}>
                            {
                                this.state.loading ?
                                    <img alt="" src={require("../assets/images/loading.gif")} style={{ width: "25px", filter: "brightness(20)" }} />
                                    : "ADD PROJECT"
                            }
                        </Button>

                    </Modal.Footer>
                </form>
            </Modal>
                <div className="container-fluid p-0">
                    <Navigation onProjectChange={this.refreshPage} name="Project" />
                    <Sidebar />
                    {(this.state.isProjectClicked) ?
                        <div className="content-wrapper">
                            <div className="section-container">
                                <PageHeader
                                    back={<i onClick={this.goBack} className="fas fa-arrow-left"></i>}
                                    heading={this.state.selectedProject.name} subHeading="" />
                                <div className="row">
                                    {(!this.state.loading) ?
                                        <ProjectDetails servers={this.state.servers} project={this.state.selectedProject} projectClickHandler={this.goBack} />
                                        :
                                        <div className='col-12 text-center'>
                                            <img alt="" src={require("../assets/images/loading.gif")} style={{ width: "100px", filter: "brightness(1)" }} />
                                        </div>}
                                </div>
                            </div>
                        </div>
                        :
                        <div className="content-wrapper">
                            <div className="section-container">
                                {(this.state.loading) ? <Loader /> :
                                    <>
                                        <PageHeader
                                            heading="My Projects"
                                            subHeading={this.state.projects.length + " Projects"}>
                                            <div className="row">
                                                <div className="col-md-4 offset-lg-8">
                                                    <button type="button" onClick={this.handleModalShow} className="btn btn-theme btn-block">
                                                        <span>Create Project</span>
                                                        <i className="fa fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>

                                        </PageHeader>
                                        <div className="row">
                                            <div className="col-12">
                                                <Alert show={(this.state.accessStatus !== null && this.state.accessStatus !== "Your projects") ? true : false} variant="info">
                                                    {this.state.accessStatus}
                                                </Alert>
                                            </div>
                                            {(!this.state.loading) ?
                                                this.renderProjects() :
                                                <div className='col-12 text-center'>
                                                    <img alt="" src={require("../assets/images/loading.gif")} style={{ width: "100px", filter: "brightness(1)" }} />
                                                </div>}
                                        </div>
                                    </>
                                }
                            </div>
                        </div>
                    }
                </div>
            </>

        )
    }
}
export default withRouter(Projects);
