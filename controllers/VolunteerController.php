<?php

require_once __DIR__ . '/../models/Volunteer/Volunteer.php';

class VolunteerController
{

    public function Index()
    {
        require_once 'views/Volunteer/index.html';
    }

    public function VolunteerForm()
    {
        require_once 'views/Volunteer/addVolunteer.html';
    }

    public function addVolunteer($data)
    {
        $name = $data['name'];
        $email = $data['email'];
        $phone = $data['phone'];

        if (Volunteer::emailExists($email)) {
            echo "<script>alert('Error: Email already exists.'); window.history.back();</script>";
            return;
        }

        if (Volunteer::create($name, $email, $phone)) {
            echo "<script>alert('Volunteer added successfully!'); window.location.href='index.php?action=VolunteerDepartment';</script>";
        } else {
            echo "<script>alert('Error adding volunteer.'); window.history.back();</script>";
        }
    }

    public function editVolunteer($id)
    {
        $volunteer = Volunteer::getById((int) $id);
        if (!$volunteer) {
            echo "<script>alert('Volunteer not found.'); window.location.href='index.php?action=showVolunteers';</script>";
            return;
        }
        require_once 'views/Volunteer/editVolunteer.html';
    }

    public function updateVolunteer($data)
    {
        $id = (int) $data['id'];
        $name = $data['name'];
        $email = $data['email'];
        $phone = $data['phone'];

        $volunteer = Volunteer::getById($id);
        if (!$volunteer) {
            echo "<script>alert('Volunteer not found.'); window.history.back();</script>";
            return;
        }

        if (Volunteer::emailExists($email, $id)) {
            echo "<script>alert('Error: Email already exists for another volunteer.'); window.history.back();</script>";
            return;
        }

        $volunteer->setName($name);
        $volunteer->setEmail($email);
        $volunteer->setPhone($phone);

        if ($volunteer->update()) {
            echo "<script>alert('Volunteer updated successfully!'); window.location.href='index.php?action=showVolunteers';</script>";
        } else {
            echo "<script>alert('Error updating volunteer.'); window.history.back();</script>";
        }
    }

    public function deleteVolunteer($id)
    {
        $volunteer = Volunteer::getById((int) $id);
        if (!$volunteer) {
            echo "<script>alert('Volunteer not found.'); window.location.href='index.php?action=showVolunteers';</script>";
            return;
        }

        if (Volunteer::delete((int) $id)) {
            echo "<script>alert('Volunteer deleted successfully!'); window.location.href='index.php?action=showVolunteers';</script>";
        } else {
            echo "<script>alert('Error deleting volunteer.'); window.history.back();</script>";
        }
    }

    public function showVolunteers()
    {
        $volunteers = Volunteer::getVolunteers();
        require_once 'views/Volunteer/showVolunteers.html';
    }

}

