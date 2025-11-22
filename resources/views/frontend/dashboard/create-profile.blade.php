@extends('frontend.layouts.app')

@section('content')
@include('frontend.partials.header')

<section class="filter-section">
    <div class="filter-card">
        <form id="uniformFilterForm">
            @if(isset($profile) && $profile)
                <input type="hidden" name="_method" value="PUT">
            @endif
            <div class="filter-group">
                <label class="filter-label" for="schoolName">School Name</label>
                <div class="autocomplete-wrapper">
                    <input type="text" id="schoolName" name="school_name" class="filter-input" placeholder="Start typing school name" autocomplete="off" value="{{ isset($profile) && $profile ? $profile['school_name'] : '' }}" required>
                    <div class="suggestion-list" id="schoolSuggestions" aria-live="polite"></div>
                </div>
            </div>
            <div class="filter-group">
                <label class="filter-label" for="studentName">Student Name</label>
                <input type="text" id="studentName" name="student_name" class="filter-input" placeholder="Enter student name" value="{{ isset($profile) && $profile ? $profile['student_name'] : '' }}" required>
            </div>
            <div class="filter-group">
                <label class="filter-label">Grade</label>
                <div class="filter-buttons">
                    <button type="button" class="filter-btn grade-btn" data-value="LKG">LKG</button>
                    <button type="button" class="filter-btn grade-btn" data-value="UKG">UKG</button>
                    <button type="button" class="filter-btn grade-btn" data-value="1">Grade 1</button>
                    <button type="button" class="filter-btn grade-btn" data-value="2">Grade 2</button>
                    <button type="button" class="filter-btn grade-btn" data-value="3">Grade 3</button>
                    <button type="button" class="filter-btn grade-btn" data-value="4">Grade 4</button>
                    <button type="button" class="filter-btn grade-btn" data-value="5">Grade 5</button>
                    <button type="button" class="filter-btn grade-btn" data-value="6">Grade 6</button>
                    <button type="button" class="filter-btn grade-btn" data-value="7">Grade 7</button>
                    <button type="button" class="filter-btn grade-btn" data-value="8">Grade 8</button>
                    <button type="button" class="filter-btn grade-btn" data-value="9">Grade 9</button>
                    <button type="button" class="filter-btn grade-btn" data-value="10">Grade 10</button>
                    <button type="button" class="filter-btn grade-btn" data-value="11">Grade 11</button>
                    <button type="button" class="filter-btn grade-btn" data-value="12">Grade 12</button>
                </div>
                <input type="hidden" id="gradeValue" name="grade" required>
            </div>
            <div class="filter-group">
                <label class="filter-label" for="section">Section</label>
                <input type="text" id="section" name="section" class="filter-input" placeholder="Enter section (e.g., A, B, C)" value="{{ isset($profile) && $profile ? $profile['section'] : '' }}" required>
            </div>
            <div class="filter-group">
                <label class="filter-label">Gender</label>
                <div class="filter-buttons">
                    <button type="button" class="filter-btn gender-btn" data-value="male">Male</button>
                    <button type="button" class="filter-btn gender-btn" data-value="female">Female</button>
                </div>
                <input type="hidden" id="genderValue" name="gender" required>
            </div>
            <div class="form-actions">
                <button type="submit" class="submit-btn w-100">
                    {{ isset($profile) && $profile ? 'Update Profile' : 'Submit' }}
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>
</section>

<style>
    .filter-section {
        min-height: 70vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 60px 20px;
        background: linear-gradient(135deg, #ffffff 0%, #eae2ee 100%);
    }

    .filter-card {
        background: rgba(87, 38, 122, 0.95);
        border-radius: 40px;
        padding: 50px 60px;
        max-width: 700px;
        width: 100%;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    .filter-group {
        margin-bottom: 35px;
    }

    .filter-group:last-of-type {
        margin-bottom: 45px;
    }

    .filter-label {
        color: #ffffff;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 15px;
        display: block;
        font-family: 'Poppins', sans-serif;
    }

    .filter-input {
        width: 100%;
        padding: 14px 20px;
        border-radius: 30px;
        border: none;
        background: #ffffff;
        color: #5e2a84;
        font-size: 15px;
        font-family: 'Poppins', sans-serif;
        outline: none;
        transition: box-shadow 0.3s ease;
    }

    .filter-input::placeholder {
        color: rgba(94, 42, 132, 0.6);
    }

    .filter-input:focus {
        box-shadow: 0 0 0 3px rgba(94, 42, 132, 0.25);
    }

    .autocomplete-wrapper {
        position: relative;
    }

    .suggestion-list {
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        right: 0;
        background: #ffffff;
        border-radius: 18px;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        display: none;
        z-index: 5;
    }

    .suggestion-item {
        padding: 12px 18px;
        font-size: 14px;
        color: #5e2a84;
        cursor: pointer;
        transition: background 0.2s ease;
    }

    .suggestion-item:hover {
        background: rgba(94, 42, 132, 0.08);
    }

    .suggestion-item img {
        flex-shrink: 0;
    }

    .filter-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .filter-btn {
        background: #ffffff;
        color: #5e2a84;
        border: 2px solid transparent;
        border-radius: 25px;
        padding: 10px 24px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        font-family: 'Poppins', sans-serif;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    }

    .filter-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    .filter-btn.active {
        background: #ff1744;
        color: #ffffff;
        border-color: #ff1744;
        box-shadow: 0 12px 28px rgba(255, 23, 68, 0.3);
    }

    .form-actions {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 16px;
    }

    .secondary-btn {
        background: #ffffff;
        color: #5e2a84;
        border: none;
        border-radius: 30px;
        padding: 12px 28px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        font-family: 'Poppins', sans-serif;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        transition: all 0.2s ease;
    }

    .secondary-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    .submit-btn {
        background: #ff1744;
        color: #ffffff;
        border: none;
        border-radius: 30px;
        padding: 14px 50px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        margin: 0;
        font-family: 'Poppins', sans-serif;
    }

    .submit-btn:hover {
        background: #d01437;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(255, 23, 68, 0.4);
    }

    .submit-btn i {
        font-size: 18px;
    }

    @media (max-width: 768px) {
        .filter-card {
            padding: 40px 30px;
            border-radius: 30px;
        }

        .filter-label {
            font-size: 16px;
        }

        .filter-btn {
            padding: 8px 18px;
            font-size: 13px;
        }

        .form-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .secondary-btn,
        .submit-btn {
            width: 100%;
            justify-content: center;
        }

        .submit-btn {
            padding: 12px 40px;
            font-size: 15px;
        }
    }

    @media (max-width: 480px) {
        .filter-card {
            padding: 30px 20px;
        }

        .filter-buttons {
            gap: 8px;
        }

        .filter-btn {
            padding: 8px 16px;
            font-size: 12px;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Grade and Gender button selection (single select)
    document.querySelectorAll('.filter-buttons').forEach(group => {
        const buttons = group.querySelectorAll('.filter-btn');
        const isMultiSelect = group.dataset.multi === 'true';

        buttons.forEach(button => {
            button.addEventListener('click', () => {
                if (isMultiSelect) {
                    button.classList.toggle('active');
                } else {
                    buttons.forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');
                    
                    // Update hidden input values
                    if (button.classList.contains('grade-btn')) {
                        document.getElementById('gradeValue').value = button.dataset.value;
                    } else if (button.classList.contains('gender-btn')) {
                        document.getElementById('genderValue').value = button.dataset.value;
                    }
                }
            });
        });
    });

    // School name autocomplete with logos
    const schools = [
        {
            name: 'Stanes ICSE School',
            location: 'Peelamedu',
            logo: '{{ asset("assets/img/school_logo/Stanes ICSE logo.png") }}'
        },
        {
            name: 'Stanes School CBSE',
            location: 'Avinashi Road',
            logo: '{{ asset("assets/img/school_logo/Stanes School CBSE logo.jpg") }}'
        },
        {
            name: 'Stanes Anglo Indian Higher Secondary School (AIHSS) – Samacheer',
            location: 'Avinashi Road',
            logo: '{{ asset("assets/img/school_logo/Stanes Anglo Indian Higher Secondary School (AIHSS) – Samacheer logo.png") }}'
        },
        {
            name: 'Bharatiya Vidya Bhavan Matric Higher Secondary School (BVB) – RS Puram',
            location: 'R S Puram',
            logo: '{{ asset("assets/img/school_logo/Bharatiya Vidya Bhavan Matric Higher Secondary School (BVB) – RS Puram logo.jpg") }}'
        },
        {
            name: 'Bharatiya Vidya Bhavan Matric Higher Secondary School (BVB) – Ajjanur',
            location: 'Ajjanur',
            logo: '{{ asset("assets/img/school_logo/Bharatiya Vidya Bhavan Matric Higher Secondary School (BVB) – Ajjanur logo.jpg") }}'
        },
        {
            name: 'Shri Nehru Vidyalaya Matriculation Higher Secondary School (SNV)',
            location: 'R.S. Puram',
            logo: null // No logo file found
        }
    ];

    const schoolInput = document.getElementById('schoolName');
    const suggestionBox = document.getElementById('schoolSuggestions');

    const renderSuggestions = (value) => {
        const query = value.trim().toLowerCase();
        if (!query) {
            suggestionBox.style.display = 'none';
            suggestionBox.innerHTML = '';
            return;
        }

        const matches = schools.filter(school => 
            school.name.toLowerCase().includes(query) || 
            school.location.toLowerCase().includes(query)
        );
        
        if (matches.length === 0) {
            suggestionBox.style.display = 'none';
            suggestionBox.innerHTML = '';
            return;
        }

        suggestionBox.innerHTML = matches.map(school => 
            `<div class="suggestion-item" data-value="${school.name}">
                <div class="d-flex align-items-center gap-2">
                    ${school.logo ? `<img src="${school.logo}" alt="${school.name}" style="width: 30px; height: 30px; object-fit: contain; border-radius: 4px;">` : '<div style="width: 30px; height: 30px; background: #e0d5f0; border-radius: 4px;"></div>'}
                    <div>
                        <div style="font-weight: 600;">${school.name}</div>
                        <div style="font-size: 12px; color: #666;">${school.location}</div>
                    </div>
                </div>
            </div>`
        ).join('');
        suggestionBox.style.display = 'block';
    };

    schoolInput.addEventListener('input', (e) => {
        renderSuggestions(e.target.value);
    });

    suggestionBox.addEventListener('click', (e) => {
        const item = e.target.closest('.suggestion-item');
        if (item) {
            schoolInput.value = item.dataset.value;
            suggestionBox.style.display = 'none';
            suggestionBox.innerHTML = '';
        }
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.autocomplete-wrapper')) {
            suggestionBox.style.display = 'none';
        }
    });

    // Pre-fill form if editing
    @if(isset($profile) && $profile)
        // Pre-select grade
        const gradeValue = '{{ $profile["grade"] }}';
        if (gradeValue) {
            const gradeBtn = document.querySelector(`.grade-btn[data-value="${gradeValue}"]`);
            if (gradeBtn) {
                gradeBtn.click();
            }
        }
        
        // Pre-select gender
        const genderValue = '{{ $profile["gender"] }}';
        if (genderValue) {
            const genderBtn = document.querySelector(`.gender-btn[data-value="${genderValue}"]`);
            if (genderBtn) {
                genderBtn.click();
            }
        }
    @endif

    // Form submission
    document.getElementById('uniformFilterForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate required fields
        const grade = document.getElementById('gradeValue').value;
        const gender = document.getElementById('genderValue').value;
        const schoolName = document.getElementById('schoolName').value;
        const studentName = document.getElementById('studentName').value;
        const section = document.getElementById('section').value;

        if (!grade || !gender || !schoolName || !studentName || !section) {
            alert('Please fill in all required fields');
            return;
        }

        // Submit form to server
        const form = document.getElementById('uniformFilterForm');
        @if(isset($profile) && $profile)
            form.action = '{{ route("frontend.parent.update-profile", ["profileId" => $profile["id"]]) }}';
        @else
            form.action = '{{ route("frontend.parent.store-profile") }}';
        @endif
        form.method = 'POST';
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        form.submit();
    });
});
</script>
@endsection
