@if($role === 'student')

    <p>
        Welcome as a student.
        You can now join teams and apply for projects.
    </p>

@elseif($role === 'mentor')

    <p>
        Welcome as a mentor.
        You can mentor teams and participate in evaluations.
    </p>

@elseif($role === 'company')

    <p>
        Welcome as a company representative.
        You can create projects and collaborate with students.
    </p>

@elseif($role === 'admin')

    <p>
        Welcome to the administration panel.
    </p>

@endif
