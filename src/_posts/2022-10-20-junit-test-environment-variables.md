---
comments_id: 78
date: 2022-10-20
title: "Unit tests for classes using Environment Variables"
tags: java, junit, environment-variables, test, system-stubs
---
I recently had the problem of moving a class using environment variables from Java 8 to Java 17. The problem was
that we were using a hack to update the environment variables with reflection in the test. Not a good idea when
updating to Java 17 as reflection is very restrictive now (e.g. you are not allowed to update final fields).

## System Stubs

I found this nice library called [System Stubs](https://github.com/webcompere/system-stubs). It allows you to mock
nearly everything related to the system (e.g. exit code, stderr and stdout, system properties, environment variables)
and provides rules for JUnit 4 and 5. Since version 2 it also supports Java 17.

In case you are still using [System Lambda](https://github.com/stefanbirkner/system-lambda) you can smoothly migrate to
System Stubs as it behaves nearly the same.

## Solution

The `EnvironmentVariableConfigurationAdapter` is quite simple. It reads some environment variables and makes them
accessible through getters. The test looks like

```java
import uk.org.webcompere.systemstubs.rules.EnvironmentVariablesRule;

public class EnvironmentVariableConfigurationAdapterUnitTest {
    @ClassRule
    public static EnvironmentVariablesRule environmentVariablesRule =
            new EnvironmentVariablesRule();

    private EnvironmentVariableConfigurationAdapter adapter;

    @Before
    public void setUp() {
        adapter = new EnvironmentVariableConfigurationAdapter();
    }

    @Test
    public void shouldReturnTheStandAloneClient_whenGetClientId_givenVariableIsSet() {
        // given
        environmentVariablesRule.set("STANDALONE_CLIENT", "1");

        // then
        assertThat(adapter.getClientId()).isEqualTo("1");
    }
}
```

In case of parallel test execution you have to consider that the environment variables are shares among all threads. So either
make sure that you fork the JVM or do not use the rule, but

```java
import uk.org.webcompere.systemstubs.environment.EnvironmentVariables;

public class EnvironmentVariableConfigurationAdapterUnitTest {
    private EnvironmentVariableConfigurationAdapter adapter;

    @Before
    public void setUp() {
        adapter = new EnvironmentVariableConfigurationAdapter();
    }
    
    @Test
    public void shouldReturnTheStandAloneClient_whenGetClientId_givenVariableIsSet() {
        // given
        withEnvironmentVariable("STANDALONE_CLIENT", "1").execute(() -> {
            // then
            assertThat(adapter.getClientId()).isEqualTo("1");
        });
    }
}
```

Now reading this refactored version, I will get rid of the rule in th example above. This one seems to be the better alternative.
