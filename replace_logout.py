import os, glob

for file in glob.glob('flask_app/templates/**/*.html', recursive=True):
    try:
        with open(file, 'r', encoding='utf-8') as f:
            lines = f.readlines()
        
        modified = False
        new_lines = []
        for index in range(len(lines)):
            line = lines[index]
            if 'Cerrar Sesión' in line:
                # check if previous line has href="/login"
                prev_line = lines[index-1] if index > 0 else ""
                prev_prev_line = lines[index-2] if index > 1 else ""
                if 'href="/login"' in prev_line:
                    lines[index-1] = prev_line.replace('href="/login"', 'href="/logout"')
                    modified = True
                elif 'href="/login"' in prev_prev_line:
                    lines[index-2] = prev_prev_line.replace('href="/login"', 'href="/logout"')
                    modified = True
                # or if it's in the same line
                if 'href="/login"' in line:
                    line = line.replace('href="/login"', 'href="/logout"')
                    modified = True
            new_lines.append(line)
        
        if modified:
            with open(file, 'w', encoding='utf-8') as f:
                f.writelines(lines)
            print(f"Modified {file}")
    except Exception as e:
        print(f"Error reading {file}: {e}")
