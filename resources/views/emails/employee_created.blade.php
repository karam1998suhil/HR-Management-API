<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; padding: 20px;">

    <h2>Hello, {{ $manager->name }} 👋</h2>

    <p>A new employee has been added to your team:</p>

    <table border="1" cellpadding="8" cellspacing="0">
        <tr><td><strong>Name</strong></td><td>{{ $employee->name }}</td></tr>
        <tr><td><strong>Email</strong></td><td>{{ $employee->email }}</td></tr>
        <tr><td><strong>Salary</strong></td><td>${{ number_format($employee->salary, 2) }}</td></tr>
    </table>

    <p>Please welcome them to the team.</p>

</body>
</html> 