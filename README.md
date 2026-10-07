# Student Task Manager

## Student Information
- **Student Name:** Naw Khu Paw Moo
- **Student ID:** 202300147
- **Assigned Challenge:** Searching by title/category, status filtering, and highlighting overdue tasks.

---

## AI-Use Reflection

- **AI tool(s) used:** Gemini

### Three examples of how AI helped me:
1. **PDO Configuration:** Suggested optimal PDO attributes (`ERRMODE_EXCEPTION`, `FETCH_ASSOC`, `EMULATE_PREPARES => false`) for safe database interactions.
2. **Modular Functions:** Assisted in separating database queries into clean functions inside `functions.php`.
3. **Overdue Logic:** Showed how to compare Unix timestamps using `strtotime()` against current server dates.

### One AI-generated suggestion or piece of code that I changed or rejected:
- **What was it?** The AI initially suggested using `extract($_POST)`.
- **Why did I change/reject it?** `extract()` can introduce unexpected variable overwrites and security vulnerabilities. I explicitly mapped `$_POST` elements instead.

### The part of this application I understand least:
- How timezone offsets configured in PHP (`date.timezone`) interact with MySQL `DATE` and `DATETIME` comparisons during strict boundary evaluations.