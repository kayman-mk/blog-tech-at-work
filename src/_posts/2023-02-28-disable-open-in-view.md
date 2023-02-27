---
comments_id: 121
title: "Spring's Open In View: Better Disable it!"
date: 2023-02-28
tags: spring, jpa, performance
---
We had some performance issues today. The application was running for a year without any problems, but failed today.
It received more load than usual and showed some problems the last week. But it seemed that this problem shouldn't
become big.

Today, the application failed completely. The simple error was: `Connection is not available, request timed out after
30000ms.` This was returned by our HikariCP we used in the Spring application. The questions now is: What operation
takes such a long time and blocks the database connection? We had no idea, what was going wrong. Especially as
the average transaction time is usually around 0.2 seconds. Well, today it increased to 30+ seconds and killed
the application.

By accident, we found out that `spring.jpa.open-in-view` wasn't set to `false` (the default value is `true`). Fixing
this was the solution. But why?

If this attribute is `true`, a session is hold for the complete lifetime and an underlying database connection too.
This sounds good, especially if you have lazy relationships. You can simply access them in the controller as the
database transaction is still there. But this comes at the cost of one database connection which is held for the
whole lifetime of the transaction.

In our case some operations took a little longer as the load increased. So we were holding the database
connection a little longer too. This way all database connections were in use and new transactions weren't able
to acquire one.

Think carefully about the default values of Spring. Sometimes it is better to change them.
