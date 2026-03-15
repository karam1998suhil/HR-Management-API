<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; padding: 20px;">

    @if($notifiedPerson)
        <h2>Hello, {{ $notifiedPerson->name }} 👋</h2>
        <p>A salary change occurred for one of your team members:</p>
    @else
        <h2>Hello, {{ $employee->name }} 👋</h2>
        <p>Your salary has been updated:</p>
    @endif

    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <td><strong>Employee</strong></td>
            <td>{{ $employee->name }}</td>
        </tr>
        <tr>
            <td><strong>Old Salary</strong></td>
            <td style="color: red;">${{ number_format($oldSalary, 2) }}</td>
        </tr>
        <tr>
            <td><strong>New Salary</strong></td>
            <td style="color: green;">${{ number_format($newSalary, 2) }}</td>
        </tr>
        <tr>
            <td><strong>Change</strong></td>
            <td>
                @if($newSalary > $oldSalary)
                    ▲ +${{ number_format($newSalary - $oldSalary, 2) }}
                @else
                    ▼ -${{ number_format($oldSalary - $newSalary, 2) }}
                @endif
            </td>
        </tr>
    </table>

    <p style="color: #999; font-size: 12px;">
        This is an automated message from the HR Management System.
    </p>

</body>
</html>